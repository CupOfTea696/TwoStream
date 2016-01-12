<?php namespace CupOfTea\TwoStream\Console;

trait Writer
{
    /**
     * Set the output's indentation level.
     *
     * @param  int $level
     * @return \CupOfTea\TwoStream\Console\Writer
     */
    public function level($level)
    {
        $this->out->level($level);
        
        return $this;
    }
    
    /**
     * Write a string as information output.
     *
     * @param  string  $string
     * @param  int     $level
     * @return \CupOfTea\TwoStream\Console\Writer
     */
    public function info($string, $level = null, $verbosity = null)
    {
        $this->out->level($level)->writeln("<info>$string</info>");
        
        return $this;
    }
    
    /**
     * Write a string as standard output.
     *
     * @param  string  $string
     * @param  int     $level
     * @return \CupOfTea\TwoStream\Console\Writer
     */
    public function line($string, $level = null)
    {
        $this->out->level($level)->writeln($string);
        
        return $this;
    }
    
    /**
     * Write a string as comment output.
     *
     * @param  string  $string
     * @param  int     $level
     * @return \CupOfTea\TwoStream\Console\Writer
     */
    public function comment($string, $level = null)
    {
        $this->out->level($level)->writeln("<comment>$string</comment>");
        
        return $this;
    }
    
    /**
     * Write a string as question output.
     *
     * @param  string  $string
     * @param  int     $level
     * @return \CupOfTea\TwoStream\Console\Writer
     */
    public function question($string, $level = null)
    {
        $this->out->level($level)->writeln("<question>$string</question>");
        
        return $this;
    }
    
    /**
     * Write a string as error output.
     *
     * @param  string  $string
     * @param  int     $level
     * @return \CupOfTea\TwoStream\Console\Writer
     */
    public function error($string, $level = null)
    {
        $this->out->level($level)->writeln("<error>$string</error>");
        
        return $this;
    }
}
