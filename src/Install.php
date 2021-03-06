<?php namespace CupOfTea\TwoStream;

use App;
use Storage;
use CupOfTea\TwoStream\Console\Output;
use CupOfTea\TwoStream\Console\Command;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twostream:install';
    
    /**
     * The user's App Namespace.
     *
     * @var string
     */
    protected $appNamespace;
    
    /**
     * Console Output.
     *
     * @var \CupOfTea\TwoStream\Console\Output
     */
    protected $out;
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install TwoStream. This is required for TwoStream to work properly.';
    
    /**
     * Create a new install command instance.
     *
     * @param  string $namespace
     * @return void
     */
    public function __construct($namespace)
    {
        parent::__construct();
        
        $this->appNamespace = rtrim($namespace, '\\');
        $this->out = new Output();
    }
    
    /**
     * Fire the command.
     *
     * @return void
     */
    public function fire()
    {
        $this->line('Installing TwoStream...');
        $this->install();
        $this->line('Installation complete');
    }
    
    /**
     * Install the TwoStream package.
     *
     * @return void
     */
    public function install()
    {
        $disk = Storage::createLocalDriver([
            'driver' => 'local',
            'root'   => app_path(),
        ]);
        
        $this->info('Publishing required files', 1);
        $this->call('vendor:publish', ['--provider' => TwoStreamServiceProvider::class, '--tag' => ['required']], 2);
        
        $this->info('Applying your app\'s namespace <comment>[' . $this->appNamespace . ']</comment>', 1);
        foreach (TwoStreamServiceProvider::pathsToPublish(TwoStreamServiceProvider::class, 'required') as $required) {
            $required = str_replace(app_path(), '', $required);
            $originalFile = $disk->get($required);
            $file = str_replace('{{namespace}}', $this->appNamespace, $originalFile);
            
            if ($file != $originalFile) {
                $this->info('Setting namespace for <comment>[/app' . str_replace([app_path(), '.php', '.stub'], '', $required) . ']</comment>', 2);
                $disk->put($required, $file);
            }
            
            if (! $disk->exists(str_replace('.stub', '.php', $required))) {
                $disk->move($required, str_replace('.stub', '.php', $required));
            }
        }
        
        $this->info('Cleaning up...', 1);
        $this->level(0);
        $files = array_merge($disk->allFiles('Ws'), $disk->allFiles('Exceptions'));
        foreach ($files as $key => $file) {
            if (preg_match('/\\.stub$/', $file)) {
                $disk->delete($file);
            }
        }
    }
}
