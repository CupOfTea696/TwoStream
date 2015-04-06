<?php namespace CupOfTea\TwoStream\Facades;
/**
 * @see \Illuminate\Routing\Router
 */
class Route extends Facade {
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'CupOfTea\TwoStreams\Routing\WsRouter'; }
}
