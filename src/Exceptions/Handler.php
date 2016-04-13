<?php namespace CupOfTea\TwoStream\Exceptions;

use Exception;
use Psr\Log\LoggerInterface;
use CupOfTea\TwoStream\Console\Output;
use CupOfTea\TwoStream\Console\Writer;
use CupOfTea\TwoStream\Contracts\Exceptions\Handler as HandlerContract;

class Handler implements HandlerContract
{
    use Writer;
    
    /**
     * The log implementation.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $log;
    
    /**
     * Console output for the Application.
     *
     * @var \CupOfTea\TwoStream\Console\Output
     */
    protected $out;
    
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [];
    
    /**
     * {@inheritdoc}
     */
    public function __construct(LoggerInterface $log, Output $output)
    {
        $this->log = $log;
        $this->out = $output;
    }
    
    /**
     * {@inheritdoc}
     */
    public function report(Exception $e)
    {
        if ($this->shouldReport($e)) {
            $this->log->error($e);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function render(Exception $e)
    {
        $message = $e->getMessage();
        $lines = explode(PHP_EOL, $message);
        $first_line = array_shift($lines);
        
        $this->error("Error: {$first_line}", 2);
        $this->level(3);
        foreach ($lines as $line) {
            $this->comment($line);
        }
        
        return [
            'error' => [
                'msg' => $e->getMessage(),
                'domain' => 'php.' . str_replace('\_', '.', snake_case(get_class($e))),
                'full_error' => $e,
            ],
        ];
    }
    
    /**
     * Determine if the exception should be reported.
     *
     * @param  \Exception  $e
     * @return bool
     */
    public function shouldReport(Exception $e)
    {
        return ! $this->shouldntReport($e);
    }
    
    /**
     * Determine if the exception is in the "do not report" list.
     *
     * @param  \Exception  $e
     * @return bool
     */
    protected function shouldntReport(Exception $e)
    {
        foreach ($this->dontReport as $type) {
            if ($e instanceof $type) {
                return true;
            }
        }
        
        return false;
    }
}
