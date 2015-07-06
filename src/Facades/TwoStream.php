<?php namespace CupOfTea\TwoStream\Facades;

use Illuminate\Support\Facades\Facade;

use CupOfTea\TwoStream\Contracts\Factory;

/**
 * @see CupOfTea\TwoStream\TwoStream
 */
class TwoStream extends Facade
{
    
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return Factory::class; }
    
}
