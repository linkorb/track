<?php

namespace Track;

class Application
{
    protected $output;
    public function __construct($output)
    {
        $this->output = $output;
    }
    
    public function printLog($log)
    {
        $this->output->writeLn(
            "<info>" . str_pad($log->getId(), 4, ' ', STR_PAD_LEFT) . '</info>: <comment>' . $log->getMessage() . "</comment>"
        );
        
        $start = '?';
        if ($log->getStartedAt()) {
            $start = $log->getStartedAt()->format('H:i');
        }
        $end = '?';
        if ($log->getEndedAt()) {
            $end = $log->getEndedAt()->format('H:i');
        }
        $this->output->writeLn(
            "      started: <comment>" . $start . '</comment> ended: <comment>' .  $end . '</comment> duration: <comment>' . $log->presentDuration() . '</comment>'
        );
    }
    
    public function printLogs($logs)
    {
        $lastEnded = null;
        $lastDate = null;
        foreach ($logs as $log) {
            $newDate = $log->getStartedAt()->format('d-M-Y');
            if ($lastDate != $newDate) {
                $this->output->writeLn('<info>' . $newDate . '</info>');
                $lastDate = $newDate;
            }
            if ($lastEnded) {
                if ($log->getStartedAt()) {
                    $gap = $log->getStartedAt()->diff($lastEnded);
                    //print_r($gap);
                    if ($gap->h + $gap->i > 1) {
                        $gapText = '';
                        if ($gap->h) {
                            $gapText = $gap->h . 'h';
                        }
                        $gapText .= $gap->i . 'm';
                        $this->output->writeLn(
                            "      <error>GAP: $gapText</error>"
                        );
                    }
                }
            }
            $c = '?';
            if ($log->getCreatedAt()) {
                $c = $log->getCreatedAt()->format('Y/m/d H:i');
            }
            $this->printLog($log);
            if ($log->getEndedAt()) {
                $lastEnded = $log->getEndedAt();
            }
        }
    }
}
