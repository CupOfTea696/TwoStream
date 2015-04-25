<?php namespace CupOfTea\TwoStream\Contracts;

interface Factory
{
    
    /**
     * Get an TwoStream provider implementation.
     *
     * @param  string  $driver
     * @return \CupOfTea\TwoStream\Contracts\Provider
     */
    public function driver($driver = null);
    
}
