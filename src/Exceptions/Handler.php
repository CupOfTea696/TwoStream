<?php namespace CupOfTea\TwoStream\Exceptions;

use Exception;

use Psr\Log\LoggerInterface;

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
        
        $this->error("Error: {$first_line}");
        $this->level(3);
        foreach ($lines as $line) {
            $this->comment($line);
        }
        $this->level(2);
        
        return [
            'error' => [
                'msg' => $e->getMessage(),
                'domain' => 'php.' . str_replace('\_', '.', snake_case(get_class($e))),
                'full_error' => $e
            ]
        ];
    }
    
}
