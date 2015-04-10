<?php namespace CupOfTea\TwoStream;

use Symfony\Component\Console\Output\ConsoleOutput;

class NestedOutput extends ConsoleOutput{
    protected $lines = [];
    
    protected $prepend = '';
    
    public function writeln($messages, $type = self::OUTPUT_NORMAL){
        return parent::writeln($this->prepend . $messages);
    }
    
    public function level($level = 0){
        $this->prepend = '<comment>   ' . (str_repeat('  ', $level)) . '</comment>';
        
        return $this;
    }
}
