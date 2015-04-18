<?php namespace CupOfTea\TwoStream;

use Storage;

use CupOfTea\TwoStream\Routing\WsRouter;
use CupOfTea\TwoStream\Session\ReadOnly;
use CupOfTea\TwoStream\Foundation\Support\Providers\WsRouteServiceProvider;

use Illuminate\Console\AppNamespaceDetectorTrait as AppNamespaceDetector;

class TwoStreamServiceProvider extends WsRouteServiceProvider {
    
    use AppNamespaceDetector;
    
    /**
	 * This namespace is applied to the controller routes in your routes file.
	 *
	 * @var string
	 */
	protected $namespace = '{{namespace}}Ws\Controllers';

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
     * @param  \CupOfTea\TwoStream\Routing\WsRouter  $router
     * @return void
     */
    public function boot(WsRouter $router){
        $this->namespace = str_replace('{{namespace}}', $this->getAppNamespace(), $this->namespace);
        
        parent::boot($router);
        
        $this->publishes([
            __DIR__.'/../../app/Ws/Kernel.stub' => app_path('Ws/Kernel.stub'),
        ], 'required');
        
        $this->publishes([
            __DIR__.'/../../config/twostream.php' => config_path('twostream.php'),
        ], 'config');
    }
    
    /**
	 * Define the routes for the application.
	 *
	 * @param  \CupOfTea\TwoStream\Routing\WsRouter  $router
	 * @return void
	 */
	public function map(WsRouter $router){
		$router->group(['namespace' => $this->namespace], function($router){
			require app_path('Ws/routes.php');
		});
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
        
        if(!$this->isInstalled())
            return $this->commands(['command.twostream.install']);
        
		parent::register();
        
        $this->app['command.twostream.listen'] = $this->app->share(function($app){
            return new Server($app);
        });
        
        $this->commands($this->commands);
        
        $this->mergeConfigFrom(
            __DIR__.'/../../config/twostream.php', 'twostream'
        );
        
		$this->app->bindShared('CupOfTea\TwoStream\Contracts\Factory', function($app){
            $config = $this->app['config']['twostream'];
            
			return new TwoStream($config);
		});
        
		$this->app->bindShared('CupOfTea\TwoStream\Contracts\Session\ReadOnly', function($app){
            return new ReadOnly($this->app['config']['session.cookie']);
		});
	}
    
	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array_merge([
            'CupOfTea\TwoStream\Contracts\Factory',
            'CupOfTea\TwoStream\Contracts\Session\ReadOnly',
        ], parent::provides());
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

}

