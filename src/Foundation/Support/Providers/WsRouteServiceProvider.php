<?php namespace CupOfTea\TwoStream\Foundation\Support\Providers;

use CupOfTea\Package\ServiceProvider;
use CupOfTea\TwoStream\Routing\WsRouter;
use CupOfTea\TwoStream\Contracts\Routing\Registrar;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Routing\UrlGenerator;

class WsRouteServiceProvider extends ServiceProvider
{
    
    /**
     * The controller namespace for the application.
     *
     * @var string|null
     */
    protected $namespace;
    
    /**
     * Bootstrap any application services.
     *
     * @param  \CupOfTea\TwoStream\Routing\WsRouter  $router
     * @return void
     */
    public function boot(WsRouter $router)
    {
        if ($this->app->routesAreCached()) {
            $this->loadCachedRoutes();
        } else {
            $this->loadRoutes();
        }
    }
    
    /**
     * Load the cached routes for the application.
     *
     * @return void
     */
    protected function loadCachedRoutes()
    {
        $this->app->booted(function() {
            require $this->app->getCachedRoutesPath();
        });
    }
    
    /**
     * Load the application routes.
     *
     * @return void
     */
    protected function loadRoutes()
    {
        $this->app->call([$this, 'map']);
    }
    
    /**
     * Load the standard routes file for the application.
     *
     * @param  string  $path
     * @return void
     */
    protected function loadRoutesFrom($path)
    {
        $router = $this->app[WsRouter::class];
        if (is_null($this->namespace)) return require $path;
        $router->group(['namespace' => $this->namespace], function($router) use ($path)
        {
            require $path;
        });
    }
    
    /**
     * Define the routes for the application.
     *
     * @param  \CupOfTea\TwoStream\Routing\WsRouter  $router
     * @return void
     */
    public function map(WsRouter $router)
    {
        $router->group(['namespace' => $this->namespace], function($router) {
            require app_path('Ws/routes.php');
        });
    }
    
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared(WsRouter::class, function($app)
        {
            return new WsRouter($app->make(Dispatcher::class), $app);
        });
        
        $this->app->bindShared(Registrar::class, function($app)
        {
            return new WsRouter($app->make(Dispatcher::class), $app);
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
            WsRouter::class,
            Registrar::class,
        ];
    }
    
    /**
     * Pass dynamic methods onto the router instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->app[WsRouter::class], $method], $parameters);
    }
    
}
