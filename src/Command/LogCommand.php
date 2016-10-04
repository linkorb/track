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
use DateTime;

class LogCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->ignoreValidationErrors();

        $this
            ->setName('log')
            ->setDescription('Add a log')
            ->addArgument(
                'message',
                InputArgument::OPTIONAL,
                'Logs a message'
            )
            ->addOption(
                'duration',
                'd',
                InputOption::VALUE_REQUIRED,
                'Duration in minutes'
            )
            ->addOption(
                'start',
                's',
                InputOption::VALUE_OPTIONAL,
                'Start the log (no endstamp), optionally providing minutes ago',
                0
            )
            ->addOption(
                'auto',
                'a',
                InputOption::VALUE_NONE,
                'Auto define start/end stamp'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = new Application($output);
        $helper = $this->getHelper('question');
        $message = $input->getArgument('message');
        $duration = $input->getOption('duration');

        $output->writeLn(
            "Logging a message: <info>" . $message . '</info>'
        );


        $repo = new JsonLogRepository();
        $id = $repo->getMaxId() + 1;
        $log = new Log();
        $log->setId($id);
        $now = new DateTime();
        $log->setCreatedAt($now);


        $question = new Question('<info>Category</info> (<comment>' . $log->getCategory() . '</comment>): ', $log->getCategory());
        $categories = [];
        foreach ($app->getCategories() as $c) {
            $categories[] = $c->getName();
        }
        $question->setAutocompleterValues($categories);
        
        $category = $helper->ask($input, $output, $question);
        $log->setCategory($category);

        while (!$message) {
            $question = new Question('<info>Message</info> (<comment>' . $log->getMessage() . '</comment>): ', $log->getMessage());
            $message = $helper->ask($input, $output, $question);
        }
        $log->setMessage($message);
        
        $started = $repo->getMaxDateTime();
        $question = new Question('<info>Started at</info> (<comment>' . $started->format('H:i') . '</comment>): ', $started->format('H:i'));
        $start  = $helper->ask($input, $output, $question);
        $log->setStartedAt($start);

        $ended = new DateTime();
        $question = new Question('<info>Ended at</info> (<comment>' . $ended->format('H:i') . '</comment>): ', $ended->format('H:i'));
        $end  = $helper->ask($input, $output, $question);
        $log->setEndedAt($end);
        /*
        if ($duration) {
            $log->setEndedAt($now);
        }
        if ($input->hasParameterOption('-s') || $input->hasParameterOption('--start')) {
            $start = $input->getOption('start');
            if ($start<0) {
                throw new RuntimeException("Please provide a positive value for start minutes ago");
            }

            $log->setStartedAt((new DateTime())->setTimestamp(strtotime('-' . $start . ' minutes')));
        }
        if ($input->getOption('auto')) {
            //echo "AUTO\n";
            $last = $repo->getMaxDateTime();
            $log->setStartedAt($last);
            $log->setEndedAt($now);
        }
        */
        //print_r($log); exit();
        $repo->persist($log);
        $app->printLog($log);
    }
}
