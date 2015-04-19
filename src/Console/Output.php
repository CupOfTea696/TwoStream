<?php namespace CupOfTea\TwoStream\Console;

use Symfony\Component\Console\Output\ConsoleOutput;

class Output extends ConsoleOutput{
    protected $lines = [];
    
    protected $prepend = '';
    
    public function writeln($messages, $type = self::OUTPUT_NORMAL){
        return parent::writeln($this->prepend . $messages);
    }
    
    public function level($level = 0){
        $this->prepend = '   ' . (str_repeat('  ', $level));
        
        return $this;
    }
}
