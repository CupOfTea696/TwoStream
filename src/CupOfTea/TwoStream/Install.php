<?php namespace CupOfTea\TwoStream;

use App;
use Storage;

use Illuminate\Console\Command;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Install extends Command{
    
    /**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'twostream:install';
    
    /**
	 * The user's App Namespace.
	 *
	 * @var string
	 */
	protected $appNamespace;
    
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Install TwoStream. This is required for TwoStream to work properly.';
    
    public function __construct($namespace){
        $this->appNamespace = rtrim($namespace, '\\');
        $this->nested = new NestedOutput();
        
        parent::__construct();
    }
    
    public function fire(){
		$this->line('Installing TwoStream...');
        $this->install();
		$this->line('Installation complete');
    }
    
    public function install(){
        $this->info('Publishing required files', 1);
        $this->call('vendor:publish', ['--provider' => strtolower(TwoStream::PACKAGE), '--tag' => 'required'], 2);
        
        $this->info('Applying your app\'s namespace <comment>[' . $this->appNamespace . ']</comment>', 1);
        Storage::createLocalDriver([
            'driver' => 'local',
			'root'   => app_path(),
        ]);
        foreach(TwoStreamServiceProvider::pathsToPublish(strtolower(TwoStream::PACKAGE), 'required') as $required){
            $this->info('Setting namespace for <comment>[/app' . str_replace(app_path(), '', $required) . ']</comment>', 2);
        }
    }
    
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
