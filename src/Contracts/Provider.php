<?php namespace CupOfTea\TwoStream\Contracts;

interface Provider
{
    
    /**
     * Push a message to a client
     *
     * @param string $topic
     * @param string|array $data
     * @param string|array $recipient
     * @return void
     */
    public function push($topic, $data, $recipient);
    
}
