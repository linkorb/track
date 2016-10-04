<?php

namespace Track;

use Symfony\Component\Yaml\Yaml;
use Track\Model\Category;
use RuntimeException;

class Application
{
    protected $output;
    protected $categories = [];
    public function __construct($output)
    {
        $this->output = $output;
        $filename = $_SERVER['HOME'] . '/.track.yml';
        if (!file_exists($filename)) {
            throw new RuntimeException("Config file not found: " . $filename);
        }
        $yaml = file_get_contents($filename);
        $data = Yaml::parse($yaml);
        foreach ($data['categories'] as $name => $details) {
            $category = new Category();
            $category->setName($name);
            $this->categories[$category->getName()] = $category;
        }
    }
    
    public function printLog($log)
    {
        // $this->output->writeLn(
        //     "<info>" . str_pad($log->getId(), 4, ' ', STR_PAD_LEFT) . '</info>: <comment>' . $log->getMessage() . "</comment>"
        // );
        
        $start = '?';
        if ($log->getStartedAt()) {
            $start = $log->getStartedAt()->format('H:i');
        }
        $end = '?';
        if ($log->getEndedAt()) {
            $end = $log->getEndedAt()->format('H:i');
        }
        
        // $this->output->writeLn(
        //     "      started: <comment>" . $start . '</comment> ended: <comment>' .  $end . '</comment> duration: <comment>' . $log->presentDuration() . '</comment>'
        // );

        $this->output->writeLn(
            '<info>' . str_pad($log->getId(), 4, ' ', STR_PAD_LEFT) . '</info> ' . 
            '<comment>' . $start . '</comment> <comment>' .  $end . '</comment> ' .
            '<comment>' . str_pad($log->presentDuration(), 6, ' ', STR_PAD_LEFT) . '</comment>' .
            ' <info>' . str_pad($log->getCategory(), 20, ' ') . '</info>' .
            '' . $log->getMessage() . ''
        );

    }
    
    public function formatDuration($duration)
    {
        $minutes = 0;
        if ($duration->h) {
            $minutes += $duration->h * 60;
        }
        $minutes += $duration->i;
        return str_pad($minutes, 8, ' ', STR_PAD_LEFT) . 'm';
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
    
    public function reportLogs($logs, $breakdown = false)
    {
        $lastEnded = null;
        $lastDate = null;
        $categories = [];
        foreach ($logs as $log) {
            $categories[] = $log->getCategory();
        }
        $categories = array_unique($categories);
        asort($categories);

        $x = [];
        foreach ($categories as $category) {
            $duration = $this->getCategoryDuration($category, $logs);
            $x[$category] = $duration;
        }
        asort($x);
        $x = array_reverse($x);
        //print_r($x);exit();
        foreach ($x as $name=>$value) {
            $this->reportCategory($name, $logs, $breakdown);
        }
    }
    
    public function getCategoryDuration($category, $logs)
    {
        $e = new \DateTime('00:00');
        foreach ($logs as $log) {
            if ($log->getCategory()==$category) {
                $e->add($log->getDuration());
            }
        }
        $f = new \DateTime('00:00');
        $diff = $f->diff($e);
        $duration = 60*($diff->h) + $diff->i;
        return (int)$duration;
    }
    
    public function reportCategory($category, $logs, $breakdown = false)
    {
        $name = $category;
        if (!$name) {
            $name = 'No category';
        }
        $logCount = 0;
        $e = new \DateTime('00:00');
        foreach ($logs as $log) {
            if ($log->getCategory()==$category) {
                $e->add($log->getDuration());
                $logCount ++;
            }
        }
        $f = new \DateTime('00:00');
        $diff = $f->diff($e);
        $this->output->writeLn('<info>' . str_pad($name, 20, ' ') . '</info> ' . $this->formatDuration($diff) . ' x' . $logCount);

        if ($breakdown) {
            foreach ($logs as $log) {
                if ($log->getCategory()==$category) {
                    $c = '?';
                    if ($log->getCreatedAt()) {
                        $c = $log->getCreatedAt()->format('Y/m/d H:i');
                    }
                    $this->printLog($log);
                }
            }
        }

    }
    
    public function getCategories()
    {
        return $this->categories;
    }
}
