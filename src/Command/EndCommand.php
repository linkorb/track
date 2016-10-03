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
use RuntimeException;

class EndCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->ignoreValidationErrors();

        $this
            ->setName('end')
            ->setDescription('End log')
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
        $log->setEndedAt(new \DateTime());
        $repo->persist($log);
    }
}
