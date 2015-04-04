<?php namespace CupOfTea\TwoStream;

use Illuminate\Support\ServiceProvider;

class TwoStreamServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;
    
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
        $this->mergeConfigFrom(
            __DIR__.'/../../config/twostream.php', 'twostream'
        );
        
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
