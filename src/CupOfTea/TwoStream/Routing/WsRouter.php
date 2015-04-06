<?php namespace CupOfTea\TwoStreams\Routing;

use Illuminate\Routing\Router;
use Illuminate\Contracts\Routing\Registrar as RegistrarContract;

class WsRouter extends Router implements RegistrarContract {
    
    /**
	 * Register a new route with the router.
	 *
	 * @param  string  $uri
	 * @param  \Closure|array|string  $action
	 * @return \Illuminate\Routing\Route
	 */
	public function action($uri, $action)
	{
		return $this->addRoute(['GET', 'HEAD'], $uri, $action);
	}
    
    /**
	 * Register a new route with the router.
	 *
	 * @param  string  $uri
	 * @param  \Closure|array|string  $action
	 * @return \Illuminate\Routing\Route
	 */
	public function get($uri, $action)
	{
		return $this->addRoute(['GET', 'HEAD'], $uri, $action);
	}
	/**
	 * Register a new route with the router.
	 *
	 * @param  string  $uri
	 * @param  \Closure|array|string  $action
	 * @return \Illuminate\Routing\Route
	 */
	public function post($uri, $action)
	{
		return $this->addRoute(['GET', 'HEAD'], $uri, $action);
	}
	/**
	 * Register a new route with the router.
	 *
	 * @param  string  $uri
	 * @param  \Closure|array|string  $action
	 * @return \Illuminate\Routing\Route
	 */
	public function put($uri, $action)
	{
		return $this->addRoute(['GET', 'HEAD'], $uri, $action);
	}
	/**
	 * Register a new route with the router.
	 *
	 * @param  string  $uri
	 * @param  \Closure|array|string  $action
	 * @return \Illuminate\Routing\Route
	 */
	public function patch($uri, $action)
	{
		return $this->addRoute(['GET', 'HEAD'], $uri, $action);
	}
	/**
	 * Register a new route with the router.
	 *
	 * @param  string  $uri
	 * @param  \Closure|array|string  $action
	 * @return \Illuminate\Routing\Route
	 */
	public function delete($uri, $action)
	{
		return $this->addRoute(['GET', 'HEAD'], $uri, $action);
	}
	/**
	 * Register a new route with the router.
	 *
	 * @param  string  $uri
	 * @param  \Closure|array|string  $action
	 * @return \Illuminate\Routing\Route
	 */
	public function options($uri, $action)
	{
		return $this->addRoute(['GET', 'HEAD'], $uri, $action);
	}
    
}
