<?php namespace CupOfTea\TwoStream\Exceptions;

use Exception;

class InvalidRecipientException extends Exception
{
    /**
     * {@inheritdoc}
     */
    public function __construct($recipient, $code = 0, Exception $previous = null)
    {
        parent::__construct('Invalid recipient: ' . $recipient, $code, $previous);
    }
}
