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
use Track\Utils;
use Track\Application;
use Symfony\Component\Console\Question\Question;


use RuntimeException;

class EditCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->ignoreValidationErrors();

        $this
            ->setName('edit')
            ->setDescription('Edit log')
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
        $app = new Application($output);
        $id = $input->getArgument('id');
        $repo = new JsonLogRepository();
        $log = $repo->find($id);
        //$app->printLog($log);
        //print_r($log);
        $helper = $this->getHelper('question');
        $question = new Question('<info>Message</info> (<comment>' . $log->getMessage() . '</comment>): ', $log->getMessage());
        $answer = $helper->ask($input, $output, $question);
        $log->setMessage($answer);
        
        $question = new Question('<info>Started at</info> (<comment>' . $log->presentStartedAt() . '</comment>): ', $log->presentStartedAt());
        $answer = $helper->ask($input, $output, $question);
        $log->setStartedAt($answer);
        
        $question = new Question('<info>Ended at</info> (<comment>' . $log->presentEndedAt() . '</comment>): ', $log->presentEndedAt());
        $answer = $helper->ask($input, $output, $question);
        $log->setEndedAt($answer);
        
        $repo->persist($log);
        $app->printLog($log);
        
    }
}
