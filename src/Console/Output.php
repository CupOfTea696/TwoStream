<?php namespace CupOfTea\TwoStream\Console;

use Symfony\Component\Console\Output\ConsoleOutput;

class Output extends ConsoleOutput
{
    /**
     * String prepended to the Console output.
     *
     * @var string
     */
    protected $prepend = '   ';
    
    /**
     * {@inheritdoc}
     */
    public function writeln($messages, $type = self::OUTPUT_NORMAL)
    {
        if (is_array($messages)) {
            foreach ($messages as &$message) {
                $message = $this->prepend . $message;
            }
        } else {
            $messages = $this->prepend . $messages;
        }
        
        return parent::writeln($messages, $type);
    }
    
    /**
     * Set the output's indentation level.
     *
     * @param  int $level
     * @return \CupOfTea\TwoStream\Console\Output
     */
    public function level($level)
    {
        if ($level === null) {
            return $this;
        }
        
        $this->prepend = '   ' . (str_repeat('  ', $level));
        
        return $this;
    }
}
