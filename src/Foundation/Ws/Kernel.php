<?php namespace CupOfTea\TwoStream\Foundation\Ws;

use Auth;
use Session;
use Exception;

use Illuminate\Auth\AuthManager;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Facade;
use CupOfTea\TwoStream\Routing\WsRouter;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\TerminableMiddleware;
use CupOfTea\TwoStream\Session\ReadOnlySessionManager;
use CupOfTea\TwoStream\Contracts\Ws\Kernel as KernelContract;

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
     * @return \Illuminate\Http\Response
     */
    public function handle($request)
    {
        $this->loadGuard();
        
        try {
            $response = $this->sendRequestThroughRouter($request);
        } catch (Exception $e) {
            $this->reportException($e);
            throw($e);
        }
        $this->app['events']->fire('wskernel.handled', [$request, $response]);
        return $response;
    }
    
    protected function loadGuard()
    {
        $guard = new AuthManager($this->app);
        Auth::swap($guard);
    }
    
    /**
     * Send the given request through the middleware / router.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
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
     * @param  \Illuminate\Http\Response  $response
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
        return function($request)
        {
            $this->app->instance('request', $request);
            return $this->router->dispatch($request);
        };
    }
    
    /**
     * Report the exception to the exception handler.
     *
     * @param  \Exception  $e
     * @return void
     */
    protected function reportException(Exception $e)
    {
        $this->app['Illuminate\Contracts\Debug\ExceptionHandler']->report($e);
    }
    
    /**
     * Render the exception to a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderException($request, Exception $e)
    {
        return $this->app['Illuminate\Contracts\Debug\ExceptionHandler']->render($request, $e);
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
