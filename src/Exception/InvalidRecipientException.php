<?php namespace CupOfTea\TwoStream\Exception;

use Exception;

class InvalidRecipientException extends Exception
{
    public function __construct($recipient, $code = 0, Exception $previous = null)
    {
        parent::__construct('Invalid recipient: ' . $recipient, $code, $previous);
    }
}
