<?php

namespace Track\Model;

class Log
{
    protected $id;
    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
    
    protected $message;
        
    public function getMessage()
    {
        return $this->message;
    }
    
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }
    
    protected $category;
    
    public function getCategory()
    {
        return $this->category;
    }
    
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }
    
    
    protected $created_at;
    
    public function getCreatedAt()
    {
        return $this->created_at;
    }
    
    public function setCreatedAt($created_at)
    {
        $this->created_at = $this->normalizeDateTime($created_at);
        return $this;
    }
    
    protected $started_at;
    
    public function getStartedAt()
    {
        return $this->started_at;
    }
    
    public function setStartedAt($started_at)
    {
        $this->started_at = $this->normalizeDateTime($started_at);
        return $this;
    }
    
    protected function normalizeDateTime($d)
    {
        if (is_a($d, 'DateTime')) {
            return $d;
        }
        if (is_string($d)) {
            $stamp = strtotime($d);
            $d = new \DateTime();
            $d->setTimestamp($stamp);
            return $d;
        }
    }
    
    protected $ended_at;
    
    public function getEndedAt()
    {
        return $this->ended_at;
    }
    
    public function setEndedAt($ended_at)
    {
        $this->ended_at = $this->normalizeDateTime($ended_at);
        return $this;
    }
    
    
    public function presentStartedAt()
    {
        if (!$this->started_at) {
            return '?';
        }
        return $this->started_at->format('Y/m/d H:i');
    }
    public function presentEndedAt()
    {
        if (!$this->ended_at) {
            return '?';
        }
        return $this->ended_at->format('Y/m/d H:i');
    }
    
    public function getDuration()
    {
        if ($this->ended_at && $this->started_at) {
            return $this->started_at->diff($this->ended_at);
        }
        return new \DateInterval('PT0S');
    }
    
    public function presentDuration()
    {
        if (!$this->started_at) {
            return '?';
        }
        $ended_at = new \DateTime();
        if ($this->ended_at) {
            $ended_at = $this->ended_at;
        }
        $diff = $this->started_at->diff($ended_at);
        $o = '';
        if ($diff->h>0) {
            $o .= $diff->h . 'h';
        }
        $o .= $diff->i . 'm';
        return $o;
    }
}
