<?php namespace CupOfTea\TwoStream\Foundation\Support\Providers;

use CupOfTea\Package\ServiceProvider;
use CupOfTea\TwoStream\Routing\WsRouter;

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
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function boot(WsRouter $router)
    {
        $this->setRootControllerNamespace();
        if ($this->app->routesAreCached()) {
            $this->loadCachedRoutes();
        } else {
            $this->loadRoutes();
        }
    }
    
    /**
     * Set the root controller namespace for the application.
     *
     * @return void
     */
    protected function setRootControllerNamespace()
    {
        if (is_null($this->namespace)) return;
        $this->app['Illuminate\Contracts\Routing\UrlGenerator']
                        ->setRootControllerNamespace($this->namespace);
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
        $router = $this->app['CupOfTea\TwoStream\Routing\WsRouter'];
        if (is_null($this->namespace)) return require $path;
        $router->group(['namespace' => $this->namespace], function($router) use ($path)
        {
            require $path;
        });
    }
    
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('CupOfTea\TwoStream\Routing\WsRouter', function($app)
        {
            return new WsRouter($app->make('Illuminate\Contracts\Events\Dispatcher'), $app);
        });
        
        $this->app->bindShared('CupOfTea\TwoStream\Contracts\Routing\Registrar', function($app)
        {
            return new WsRouter($app->make('Illuminate\Contracts\Events\Dispatcher'), $app);
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
            'CupOfTea\TwoStream\Routing\WsRouter',
            'CupOfTea\TwoStream\Contracts\Routing\Registrar',
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
        return call_user_func_array([$this->app['CupOfTea\TwoStream\Routing\WsRouter'], $method], $parameters);
    }
    
}
