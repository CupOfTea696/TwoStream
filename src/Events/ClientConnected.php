<?php namespace CupOfTea\TwoStream\Events;

class ClientConnected
{
    
    public $client;
    
    /**
     * Create a new event instance.
     *
     * @param  string  $client
     * @return void
     */
    public function __construct($client)
    {
        $this->client = $client;
    }
    
}
