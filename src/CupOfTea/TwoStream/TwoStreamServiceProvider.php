<?php namespace CupOfTea\TwoStream;

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
            __DIR__.'/../../app/Ws/Kernel.php' => app_path('Ws/Kernel.php'),
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
            return new Server();
        });
        
        $this->commands($this->commands);
        
        $this->mergeConfigFrom(
            __DIR__.'/../../config/twostream.php', 'twostream'
        );
        
		$this->app->bindShared('CupOfTea\TwoStream\Contracts\Factory', function($app){
            $config = $this->app['config']['twostream'];
            $kernel = $this->kernel();
            
			return new TwoStream($config);
		});
        
        $this->app->bindShared('CupOfTea\TwoStream\Contracts\Dispatcher', function($app){
            return new Dispatcher();
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
            'CupOfTea\TwoStream\Contracts\Dispatcher',
        ];
	}
    
    protected function kernel(){
        $this->app->singleton(
            'CupOfTea\TwoStream\Contracts\Ws\Kernel',
            $this->getAppNamespace() . 'Ws\Kernel'
        );
        
        return $this->app->make('CupOfTea\TwoStream\Contracts\Ws\Kernel');
    }

}
