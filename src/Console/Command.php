<?php namespace CupOfTea\TwoStream\Console;

use Illuminate\Console\Command as ConsoleCommand;
use Symfony\Component\Console\Input\ArrayInput;

abstract class Command extends ConsoleCommand
{
    use Writer;
    
    /**
     * {@inheritdoc}
     */
    public function call($command, array $arguments = [], $level = 0)
    {
        $instance = $this->getApplication()->find($command);
        $arguments['command'] = $command;
        
        return $instance->run(new ArrayInput($arguments), $this->out->level($level));
    }
}
