<?php

namespace Track\Command;

use Symfony\Component\Console\Helper\DescriptorHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Track\Model\Log;
use Track\Repository\JsonLogRepository;
use Track\Application;

use RuntimeException;

class ReportCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->ignoreValidationErrors();

        $this
            ->setName('report')
            ->setDescription('Report breakdown log')
            ->addOption(
                'filter',
                'f',
                InputOption::VALUE_REQUIRED,
                'Filter string'
            )
            ->addOption(
                'breakdown',
                'b',
                InputOption::VALUE_NONE,
                'Breakdown'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filter = $input->getOption('filter');
        $breakdown = $input->getOption('breakdown');
        $repo = new JsonLogRepository();
        $logs = $repo->findAll();
        $res = [];
        $e = new \DateTime('00:00');
        foreach ($logs as $log) {
            $include = true;
            if ($filter) {
                if (!stripos(' ' . $log->getMessage(), $filter)) {
                    $include = false;
                }
            }
            if ($include) {
                $res[] = $log;
                $e->add($log->getDuration());
            }
        }
        $app = new Application($output);
        $app->reportLogs($res, $breakdown);
    }
}
