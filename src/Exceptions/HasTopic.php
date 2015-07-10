<?php namespace CupOfTea\TwoStream\Exceptions;

use Exception;

class BaseException extends Exception
{
    
    protected $topic;
    
    public function getTopic()
    {
        return $this->topic;
    }
    
    public function setTopic($topic)
    {
        $this->topic = $topic;
        
        return $this;
    }
    
}