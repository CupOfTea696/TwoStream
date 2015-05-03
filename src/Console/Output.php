<?php namespace CupOfTea\TwoStream\Console;

use Symfony\Component\Console\Output\ConsoleOutput;

class Output extends ConsoleOutput
{
    
    /**
     * String prepended to the Console output.
     *
     * @var string
     */
    protected $prepend = '';
    
    /**
     * {@inheritdoc}
     */
    public function writeln($messages, $type = self::OUTPUT_NORMAL)
    {
        return parent::writeln($this->prepend . $messages);
    }
    
    /**
     * Set the output's indentation level
     *
     * @var int
     * @default 0
     */
    public function level($level = 0)
    {
        $this->prepend = '   ' . (str_repeat('  ', $level));
        
        return $this;
    }
    
}
