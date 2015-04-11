<?php namespace CupOfTea\TwoStream;

use Storage;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\AppNamespaceDetectorTrait as AppNamespaceDetector;

class TwoStreamServiceProvider extends ServiceProvider {
    
    use AppNamespaceDetector;

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;
    
    /**
	 * Available commands in this package
	 *
	 * @var array
	 */
    protected $commands = [
        'command.twostream.install',
        'command.twostream.listen',
    ];
    
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot(){
        $this->publishes([
            __DIR__.'/../../app/Ws/Kernel.stub' => app_path('Ws/Kernel.stub'),
        ], 'required');
        
        $this->publishes([
            __DIR__.'/../../config/twostream.php' => config_path('twostream.php'),
        ], 'config');
    }

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register(){
        $this->app['command.twostream.install'] = $this->app->share(function($app){
            return new Install($this->getAppNamespace());
        });
        
        $this->app['command.twostream.listen'] = $this->app->share(function($app){
            return new Server($this->Kernel());
        });
        
        $this->commands($this->commands);
        
        $this->mergeConfigFrom(
            __DIR__.'/../../config/twostream.php', 'twostream'
        );
        
		$this->app->bindShared('CupOfTea\TwoStream\Contracts\Factory', function($app){
            $config = $this->app['config']['twostream'];
            $Kernel = $this->Kernel();
            
			return new TwoStream($config);
		});
	}
    
	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return [
            'CupOfTea\TwoStream\Contracts\Factory',
        ];
	}
    
    protected function isInstalled(){
        $disk = Storage::createLocalDriver([
            'driver' => 'local',
			'root'   => app_path(),
        ]);
        
        foreach(TwoStreamServiceProvider::pathsToPublish(strtolower(TwoStream::PACKAGE), 'required') as $required){
            if(!$disk->exists(str_replace('.stub', '.php', $required)))
                return false;
        }
        
        return true;
    }
    
    protected function Kernel(){
        if(!$this->isInstalled())
            return false;
        
        $this->app->singleton(
            'CupOfTea\TwoStream\Contracts\Ws\Kernel',
            $this->getAppNamespace() . 'Ws\Kernel'
        );
        
        return $this->app->make('CupOfTea\TwoStream\Contracts\Ws\Kernel');
    }

}
