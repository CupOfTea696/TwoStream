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
        'CupOfTea\TwoStream\Server',
    ];
    
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot(){
        $this->publishes([
            __DIR__.'/../../config/twostream.php' => config_path('twostream.php'),
        ], 'config');
    }

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $this->commands($this->commands);
        
        $this->mergeConfigFrom(
            __DIR__.'/../../config/twostream.php', 'twostream'
        );
        
//        $this->app->singleton(
//            'CupOfTea\TwoStream\Contracts\Ws\Kernel',
//            $this->getAppNamespace() . 'Ws\Kernel'
//        );
//
//        $kernel = $this->app->make('CupOfTea\TwoStream\Contracts\Ws\Kernel');
        
		$this->app->bindShared('CupOfTea\TwoStream\Contracts\Factory', function($app)
		{
            $config = $this->app['config']['twostream'];
            
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
		return ['CupOfTea\TwoStream\Contracts\Factory'];
	}

}
