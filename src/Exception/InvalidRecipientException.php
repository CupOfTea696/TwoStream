<?php namespace CupOfTea\TwoStream\Exception;

use Exception;

class InvalidRecipientException extends exception
{
    public function __construct($recipient){
        parent::__construct('Invalid recipient: ' . $recipient, );
    }
}