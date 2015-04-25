<?php namespace CupOfTea\TwoStream\Contracts;

interface Provider
{
    
    /**
     * Push a message to a client
     *
     * @param string $channel
     * @param array $message
     * @return void
     */
    public function push($channel, $message);
    
}
