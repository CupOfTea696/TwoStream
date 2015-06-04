<?php namespace CupOfTea\TwoStream;

use Exception;
use TwoStream as TwoStreamFacade;

use CupOfTea\TwoStream\Console\Output;
use CupOfTea\TwoStream\Console\Command;

class Stop extends Command
{
    
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'twostream:stop';
    
    /**
     * Console Output
     *
     * @var \CupOfTea\TwoStream\Console\Output
     */
    protected $out;
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Stop the TwoStream server.';
    
    /**
     * Create a new install command instance.
     *
     * @param  string $namespace
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->out = new Output();
    }
    
    /**
     * Fire the command
     *
     * @return void
     */
    public function fire()
    {
        $this->line('Stopping TwoStream server.');
        
        try {
            TwoStreamFacade::stop();
        } catch (Exception $e) {}
    }
    
}
