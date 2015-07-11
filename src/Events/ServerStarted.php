<?php namespace CupOfTea\TwoStream\Events;

class ServerStarted
{
    
    /**
     * Port on which the WebSocket Server is listening.
     *
     * @var int
     */
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
