<?php namespace CupOfTea\TwoStream;

use App;
use Storage;

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
        $disk = Storage::createLocalDriver([
            'driver' => 'local',
			'root'   => app_path(),
        ]);
        
        $this->info('Publishing required files', 1);
        $this->call('vendor:publish', ['--provider' => strtolower(TwoStream::PACKAGE), '--tag' => 'required'], 2);
        
        $this->info('Applying your app\'s namespace <comment>[' . $this->appNamespace . ']</comment>', 1);
        foreach(TwoStreamServiceProvider::pathsToPublish(strtolower(TwoStream::PACKAGE), 'required') as $required){
            $required = str_replace(app_path(), '', $required);
            $originalFile = $disk->get($required);
            $file = str_replace('{{namespace}}', $this->appNamespace, $originalFile);
            
            if($file != $originalFile){
                $this->info('Setting namespace for <comment>[/app' . str_replace([app_path(), '.php', '.stub'], '', $required) . ']</comment>', 2);
                
                $disk->put($required, $file);
                if(!$disk->exists(str_replace('.stub', '.php', $required)))
                    $disk->move($required, str_replace('.stub', '.php', $required));
            }
        }
        
        $this->info('Cleaning up...', 1);
        $files = $disk->allFiles('Ws');
        foreach ($files as $key => $file) {
            if(preg_match('/\\.stub$/', $file))
                $disk->delete($file);
        }
    }
    
}
