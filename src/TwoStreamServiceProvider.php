<?php namespace CupOfTea\TwoStream;

use Storage;

use CupOfTea\TwoStream\Routing\WsRouter;
use CupOfTea\TwoStream\Session\ReadOnly;
use CupOfTea\TwoStream\Contracts\Factory;
use CupOfTea\TwoStream\Foundation\Support\Providers\WsRouteServiceProvider;

use Illuminate\Console\AppNamespaceDetectorTrait as AppNamespaceDetector;

class TwoStreamServiceProvider extends WsRouteServiceProvider
{
    
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
    protected $defer = false;
    
    /**
     * The files that should be published.
     *
     * @var array
     */
    protected $files;
    
    /**
     * Available commands in this package
     *
     * @var array
     */
    protected $commands = [
        'command.twostream.listen',
        'command.twostream.stop',
    ];
    
    public function __construct($app)
    {
        parent::__construct($app);
        
        $this->files = [
            'required' => [
                __DIR__ . '/../app/Ws/routes.php' => app_path('Ws/routes.php'),
                __DIR__ . '/../app/Ws/Kernel.stub' => app_path('Ws/Kernel.stub'),
                __DIR__ . '/../app/Exceptions/WsHandler.stub' => app_path('Exceptions/WsHandler.stub'),
                __DIR__ . '/../app/Ws/Controllers/Controller.stub' => app_path('Ws/Controllers/Controller.stub'),
            ],
            'config' => [
                __DIR__ . '/../config/twostream.php' => config_path('twostream.php'),
            ],
        ];
    }
    
    /**
     * Bootstrap the application events.
     *
     * @param  \CupOfTea\TwoStream\Routing\WsRouter  $router
     * @return void
     */
    public function boot(WsRouter $router)
    {
        foreach ($this->files as $tag => $files) {
            $this->publishes($files, $tag);
        }
        
        $this->namespace = str_replace('{{namespace}}', $this->getAppNamespace(), $this->namespace);
        
        parent::boot($router);
    }
    
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        if (!$this->isInstalled()) {
            $this->app['command.twostream.install'] = $this->app->share(function($app) {
                return new Install($this->getAppNamespace());
            });
            
            return $this->commands(['command.twostream.install']);
        }
        
        parent::register();
        
        $this->app['command.twostream.listen'] = $this->app->share(function($app) {
            return new Server($app);
        });
        
        $this->app['command.twostream.stop'] = $this->app->share(function($app) {
            return new Stop();
        });
        
        $this->commands($this->commands);
        
        $this->mergeConfigFrom(
            __DIR__.'/../config/twostream.php', 'twostream'
        );
        
        $this->app->bindShared(Factory::class, function($app) {
            $config = array_dot($this->app['config']['twostream']);
            
            return new TwoStream($config);
        });
        
        $this->app->bindShared(ReadOnly::class, function($app) {
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
            Factory::class,
            ReadOnly::class,
        ], parent::provides());
    }
    
    /**
     * Check if the TwoStream Package is installed
     *
     * @return bool
     */
    protected function isInstalled()
    {
        $disk = Storage::createLocalDriver([
            'driver' => 'local',
            'root'   => app_path(),
        ]);
        
        foreach ($this->files['required'] as $required) {
            if (!$disk->exists(str_replace(['.stub', app_path()], ['.php', ''], $required))) {
                return false;
            }
        }
        
        return true;
    }
    
}
