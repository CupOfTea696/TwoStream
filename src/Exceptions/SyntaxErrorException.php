<?php namespace CupOfTea\TwoStream\Exceptions;

use Exception;

class SyntaxErrorException extends Exception
{
    
    /**
     * {@inheritdoc}
     */
    public function __construct($route, $error, $code = 0, Exception $previous = null)
    {
        $msg = 'The Route "' . $route . '" contains syntax errors and was not executed.' . PHP_EOL . $error;
        
        parent::__construct($msg, $code, $previous);
    }
    
}
