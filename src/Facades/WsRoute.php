<?php namespace CupOfTea\TwoStream\Facades;

use Illuminate\Support\Facades\Facade;
use CupOfTea\TwoStream\Routing\WsRouter;

/**
 * @see \CupOfTea\TwoStream\Routing\WsRouter
 */
class WsRoute extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return WsRouter::class;
    }
}
