<?php namespace CupOfTea\Package;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

abstract class ServiceProvider extends LaravelServiceProvider
{
    
    /**
     * {@inheritdoc}
     */
    protected function mergeConfigFrom($path, $key)
    {
        $config = $this->app['config']->get($key, []);
        
		$this->app['config']->set($key, $this->array_merge_recursive(require $path, $config));
    }
    
    /**
     * Merge arrays recursively.
     *
     * @param  array $a1
     * @param  array $a2
     * @param  array ...
     * @return array
     */
    protected function array_merge_recursive($a1, $a2)
    {
        if (!is_array($a1) || !is_array($a2) || array_keys($a1) === range(0, count($a1) - 1))
            return $a2;
        
        foreach ($a2 as $key => $val2) {
            $val1 = array_get($a1, $key, []);
            $a1[$key] = $this->array_merge_recursive($val1, $val2);
        }
        
        if (func_num_args() > 2)
            return call_user_func_array([__NAMESPACE__ . '\\' . __CLASS__, 'array_merge_recursive'], array_unshift(array_slice(func_get_args(), 2), $a1));
        
        return $a1;
    }
    
}
