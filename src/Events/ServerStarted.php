<?php namespace CupOfTea\TwoStream\Events;

class ServerStarted
{
    
    public $port;
    
    /**
     * Create a new event instance.
     *
     * @param  string  $port
     * @return void
     */
    public function __construct($port)
    {
        $this->port = $port;
    }
    
}
