<?php namespace CupOfTea\TwoStream\Contracts\Exceptions;

use Exception;
use Psr\Log\LoggerInterface;
use CupOfTea\TwoStream\Console\Output;

interface Handler
{
    /**
     * Create a new exception handler instance.
     *
     * @param  \Psr\Log\LoggerInterface  $log
     * @param  \CupOfTea\TwoStream\Console\Output  $ouput
     * @return void
     */
    public function __construct(LoggerInterface $log, Output $output);
    
    /**
     * Report an Exception.
     *
     * @param  Exception  $e
     * @return null|string|array
     */
    public function report(Exception $e);
    
    /**
     * Report an Exception.
     *
     * @param  Exception  $e
     * @return null|string|array
     */
    public function render(Exception $e);
}
