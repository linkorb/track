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

class ViewCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->ignoreValidationErrors();

        $this
            ->setName('view')
            ->setDescription('View log')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'Log ID'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getArgument('id');
        $repo = new JsonLogRepository();
        $log = $repo->find($id);
        $application = new Application($output);
        $application->printLog($log);
        //print_r($log);
    }
}
