<?php namespace CupOfTea\TwoStream;

use Illuminate\Console\Command as ConsoleCommand;

use Symfony\Component\Console\Input\ArrayInput;

abstract class Command extends ConsoleCommand{
    
    /**
     * @inheritdoc
     */
    public function call($command, array $arguments = array(), $level = 0){
		$instance = $this->getApplication()->find($command);
		$arguments['command'] = $command;
		$result = $instance->run(new ArrayInput($arguments), $this->nested->level($level));
        
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
		$this->line("<info>$string</info>", $level);
	}
	/**
	 * Write a string as standard output.
	 *
	 * @param  string  $string
	 * @return void
	 */
	public function line($string, $level = 0)
	{
		$this->output->writeln('<comment>   ' . (str_repeat('  ', $level)) . '</comment>' . $string);
	}
	/**
	 * Write a string as comment output.
	 *
	 * @param  string  $string
	 * @return void
	 */
	public function comment($string, $level = 0)
	{
		$this->line("<comment>$string</comment>", $level);
	}
	/**
	 * Write a string as question output.
	 *
	 * @param  string  $string
	 * @return void
	 */
	public function question($string, $level = 0)
	{
		$this->line("<question>$string</question>", $level);
	}
	/**
	 * Write a string as error output.
	 *
	 * @param  string  $string
	 * @return void
	 */
	public function error($string, $level = 0)
	{
		$this->line("<error>$string</error>", $level);
	}
    
}
