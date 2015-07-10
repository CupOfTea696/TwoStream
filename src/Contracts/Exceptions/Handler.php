<?php namespace CupOfTea\TwoStream\Contracts\Exceptions;

use Exception;

interface Handler
{
    
    /**
     * Create a new exception handler instance.
     *
     * @param  \Psr\Log\LoggerInterface  $log
     * @return void
     */
    public function __construct(LoggerInterface $log, Output $output);
    
    /**
     * Report an Exception
     *
     * @param  Exception  $e
     * @return null|string|array
     */
    public function report(Exception $e);
    
    /**
     * Report an Exception
     *
     * @param  Exception  $e
     * @return null|string|array
     */
    public function render(Exception $e);
    
}
