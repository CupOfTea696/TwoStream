<?php namespace CupOfTea\TwoStream\Facades;

use Illuminate\Support\Facades\Facade;
use CupOfTea\TwoStream\Contracts\Session\ReadOnly;

/**
 * @see CupOfTea\TwoStream\TwoStream
 */
class WsSession extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ReadOnly::class;
    }
}
