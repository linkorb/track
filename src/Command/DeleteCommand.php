<?php

namespace Track\Command;

use Symfony\Component\Console\Helper\DescriptorHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Question\Question;
use Track\Model\Log;
use Track\Repository\JsonLogRepository;
use Track\Application;
use RuntimeException;

class DeleteCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->ignoreValidationErrors();

        $this
            ->setName('delete')
            ->setDescription('Delete log')
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
        
        $helper = $this->getHelper('question');
        $answer = null;
        
        $question = new Question('<info>Are you sure you want to delete this log?</info> [y/n] ');
        $answer = $helper->ask($input, $output, $question);
        if ($answer == 'y') {
            $repo->delete($id);
            $output->writeLn("Deleted");
        } else {
            $output->writeLn("Cancelled");
        }
    }
}
