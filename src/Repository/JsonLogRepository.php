<?php

namespace Track\Repository;

use Track\Model\Log;
use DateTime;
use RuntimeException;

class JsonLogRepository
{
    protected $path;

    protected $logs = [];

    public function __construct()
    {
        $this->path = $_SERVER['HOME'].'/.track';
        if (!file_exists($this->path)) {
            throw new RuntimeException('track data dir does not exist: '.$this->path);
        }

        $filenames = glob($this->path.'/*.json');
        foreach ($filenames as $filename) {
            //echo $filename . "\n";
            $json = file_get_contents($filename);
            $data = json_decode($json, true);

            $log = new Log();
            $log->setId($data['id']);
            $log->setMessage($data['message']);
            if (isset($data['category'])) {
                $log->setCategory($data['category']);
            }
            $log->setCreatedAt($this->parseDateTime($data['created_at']));
            $log->setStartedAt($this->parseDateTime($data['started_at']));
            $log->setEndedAt($this->parseDateTime($data['ended_at']));
            $this->logs[$log->getId()] = $log;
        }
    }

    protected function formatDateTime($dt)
    {
        if (!$dt) {
            return null;
        }

        return $dt->format('Y/m/d H:i');
    }

    protected function parseDateTime($string)
    {
        return DateTime::createFromFormat('Y/m/d H:i', $string);
    }

    public function find($id)
    {
        return $this->logs[$id];
    }

    public function delete($id)
    {
        @unlink($this->path.'/'.$id.'.json');
    }

    public function findAll()
    {
        $logs = $this->logs;
        usort($logs, function ($a, $b) {
            return $a->getStartedAt() > $b->getStartedAt();
        });

        return $logs;
    }

    public function getMaxId()
    {
        $maxId = 0;
        foreach ($this->logs as $log) {
            if ($log->getId() > $maxId) {
                $maxId = $log->getId();
            }
        }

        return $maxId;
    }

    public function getMaxDateTime()
    {
        $max = null;
        foreach ($this->logs as $log) {
            if (!$max) {
                $max = $log->getStartedAt();
            }
            if ($log->getEndedAt()) {
                if ($log->getEndedAt() > $max) {
                    $max = $log->getEndedAt();
                }
            }
            if ($log->getStartedAt()) {
                if ($log->getStartedAt() > $max) {
                    $max = $log->getStartedAt();
                }
            }
        }

        return $max ?: new DateTime();
    }

    public function persist(Log $log)
    {
        $data = [];
        $data['id'] = $log->getId();
        $data['message'] = $log->getMessage();
        $data['category'] = $log->getCategory();
        $data['created_at'] = $this->formatDateTime($log->getCreatedAt());
        $data['started_at'] = $this->formatDateTime($log->getStartedAt());
        $data['ended_at'] = $this->formatDateTime($log->getEndedAt());
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n";
        file_put_contents($this->path.'/'.$log->getId().'.json', $json);
    }
}
