<?php namespace CupOfTea\TwoStream\Contracts\Routing;

use Closure;

interface Registrar
{
    
    /**
     * Register a new CALL route with the router.
     *
     * @param  string  $uri
     * @param  \Closure|array|string  $action
     * @return void
     */
    public function call($uri, $action);
    
    /**
     * Register a new PUBLISH route with the router.
     *
     * @param  string  $uri
     * @param  \Closure|array|string  $action
     * @return void
     */
    public function publish($uri, $action);
    
    /**
     * Register a new SUBSCRIBE route with the router.
     *
     * @param  string  $uri
     * @param  \Closure|array|string  $action
     * @return void
     */
    public function subscribe($uri, $action);
    
    /**
     * Register a new UNSUBSCRIBE route with the router.
     *
     * @param  string  $uri
     * @param  \Closure|array|string  $action
     * @return void
     */
    public function unsubscribe($uri, $action);
    
    /**
     * Register a new route with the given verbs.
     *
     * @param  array|string  $methods
     * @param  string  $uri
     * @param  \Closure|array|string  $action
     * @return void
     */
    public function match($methods, $uri, $action);
    
    /**
     * Create a route group with shared attributes.
     *
     * @param  array     $attributes
     * @param  \Closure  $callback
     * @return void
     */
    public function group(array $attributes, Closure $callback);
    
    /**
     * Register a new "before" filter with the router.
     *
     * @param  string|callable  $callback
     * @return void
     */
    public function before($callback);
    
    /**
     * Register a new "after" filter with the router.
     *
     * @param  string|callable  $callback
     * @return void
     */
    public function after($callback);
    
    /**
     * Register a new filter with the router.
     *
     * @param  string  $name
     * @param  string|callable  $callback
     * @return void
     */
    public function filter($name, $callback);
    
}
