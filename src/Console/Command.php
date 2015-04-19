<?php namespace CupOfTea\TwoStream\Console;

use Illuminate\Console\Command as ConsoleCommand;

use Symfony\Component\Console\Input\ArrayInput;

abstract class Command extends ConsoleCommand{
    
    /**
     * @inheritdoc
     */
    public function call($command, array $arguments = array(), $level = 0){
		$instance = $this->getApplication()->find($command);
		$arguments['command'] = $command;
		$result = $instance->run(new ArrayInput($arguments), $this->out->level($level));
        
        return $result;
    }
    
    /**
	 * Write a string as information output.
	 *
	 * @param  string  $string
	 * @return void
	 */
	public function info($string, $level = 0)
	{
		$this->out->level($level)->writeln("<info>$string</info>");
	}
	/**
	 * Write a string as standard output.
	 *
	 * @param  string  $string
	 * @return void
	 */
	public function line($string, $level = 0)
	{
        
		$this->out->level($level)->writeln($string);
	}
	/**
	 * Write a string as comment output.
	 *
	 * @param  string  $string
	 * @return void
	 */
	public function comment($string, $level = 0)
	{
		$this->out->level($level)->writeln("<comment>$string</comment>");
	}
	/**
	 * Write a string as question output.
	 *
	 * @param  string  $string
	 * @return void
	 */
	public function question($string, $level = 0)
	{
		$this->out->level($level)->writeln("<question>$string</question>");
	}
	/**
	 * Write a string as error output.
	 *
	 * @param  string  $string
	 * @return void
	 */
	public function error($string, $level = 0)
	{
		$this->out->level($level)->writeln("<error>$string</error>");
	}
    
}
