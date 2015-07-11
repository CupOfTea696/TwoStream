<?php namespace CupOfTea\TwoStream\Foundation\Ws;

use Log;
use Auth;
use Session;
use Exception;

use Illuminate\Auth\AuthManager;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Facade;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\TerminableMiddleware;

use CupOfTea\TwoStream\Routing\WsRouter;
use CupOfTea\TwoStream\Session\ReadOnlySessionManager;
use CupOfTea\TwoStream\Contracts\Ws\Kernel as KernelContract;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Kernel implements KernelContract
{
    
    /**
     * The application implementation.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;
    
    /**
     * The router instance.
     *
     * @var \CupOfTea\TwoStream\Routing\Router
     */
    protected $router;
    
    /**
     * The application's middleware stack.
     *
     * @var array
     */
    protected $middleware = [];
    
    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [];
    
    /**
     * Create a new HTTP kernel instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  \CupOfTea\TwoStream\Routing\WsRouter  $router
     * @return void
     */
    public function __construct(Application $app, WsRouter $router)
    {
        $this->app = $app;
        $this->app['config']['session.driver'] = 'array';
        $this->router = $router;
        foreach ($this->routeMiddleware as $key => $middleware) {
            $router->middleware($key, $middleware);
        }
    }
    
    /**
     * Handle an incoming HTTP request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     * @throws \Exception
     */
    public function handle($request)
    {
        $this->loadAuthManager();
        
        try {
            $response = $this->sendRequestThroughRouter($request);
            $response = $response->getContent();
            
            $json = json_decode($response, true);
            if (json_last_error() == JSON_ERROR_NONE) {
                $response = $json;
            }
        } catch (Exception $e) {
            if ($e instanceof NotFoundHttpException) {
                $response = $e;
            } else {
                throw $e;
            }
        }
        
        $this->app['events']->fire('wskernel.handled', [$request, $response]);
        
        return $response;
    }
    
    /**
     * Load the Auth Manager for the Request
     */
    protected function loadAuthManager()
    {
        Auth::swap(with(new AuthManager($this->app)));
    }
    
    /**
     * Send the given request through the middleware / router.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function sendRequestThroughRouter($request)
    {
        $this->app->instance('request', $request);
        Facade::clearResolvedInstance('request');
        
        return (new Pipeline($this->app))
                    ->send($request)
                    ->through($this->middleware)
                    ->then($this->dispatchToRouter());
    }
    
    /**
     * Call the terminate method on any terminable middleware.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $response
     * @return void
     */
    public function terminate($request, $response)
    {
        $routeMiddlewares = $this->gatherRouteMiddlewares($request);
        foreach (array_merge($routeMiddlewares, $this->middleware) as $middleware) {
            $instance = $this->app->make($middleware);
            if ($instance instanceof TerminableMiddleware) {
                $instance->terminate($request, $response);
            }
        }
        $this->app->terminate();
    }
    
    /**
     * Gather the route middleware for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function gatherRouteMiddlewares($request)
    {
        if ($request->route()) {
            return $this->router->gatherRouteMiddlewares($request->route());
        }
        
        return [];
    }
    
    /**
     * Add a new middleware to beginning of the stack if it does not already exist.
     *
     * @param  string  $middleware
     * @return $this
     */
    public function prependMiddleware($middleware)
    {
        if (array_search($middleware, $this->middleware) === false) {
            array_unshift($this->middleware, $middleware);
        }
        
        return $this;
    }
    
    /**
     * Add a new middleware to end of the stack if it does not already exist.
     *
     * @param  string  $middleware
     * @return $this
     */
    public function pushMiddleware($middleware)
    {
        if (array_search($middleware, $this->middleware) === false) {
            $this->middleware[] = $middleware;
        }
        
        return $this;
    }
    
    /**
     * Get the route dispatcher callback.
     *
     * @return \Closure
     */
    protected function dispatchToRouter()
    {
        return function($request) {
            $this->app->instance('request', $request);
            return $this->router->dispatch($request);
        };
    }
    
    /**
     * Get the Laravel application instance.
     *
     * @return \Illuminate\Contracts\Foundation\Application
     */
    public function getApplication()
    {
        return $this->app;
    }
    
}
