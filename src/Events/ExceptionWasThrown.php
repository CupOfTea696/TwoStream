<?php namespace CupOfTea\TwoStream\Events;

use Exception;

class ExceptionWasThrown
{
    
    public $exception;
    
    /**
     * Create a new event instance.
     *
     * @param  Exception  $e
     * @return void
     */
    public function __construct(Exception $e)
    {
        $this->exception = $e;
    }
    
}
