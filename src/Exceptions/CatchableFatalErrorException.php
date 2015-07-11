<?php namespace CupOfTea\TwoStream\Exceptions;

use Exception;

class CatchableFatalErrorException extends Exception
{
    
    /**
     * {@inheritdoc}
     */
    public function __construct($route, $error, $code = 0, Exception $previous = null)
    {
        $msg = 'Catchable fatal error encountered in Route "' . $route . '". The route was not executed.' . PHP_EOL . $error;
        
        parent::__construct($msg, $code, $previous);
    }
    
}
