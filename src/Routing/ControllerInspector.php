<?php namespace CupOfTea\TwoStream\Routing;

use Illuminate\Routing\ControllerInspector as LaravelControllerInspector;

class ControllerInspector extends LaravelControllerInspector
{
    
    /**
     * {@inheritdoc}
     */
    protected $verbs = array(
		'call', 'publish', 'subscribe', 'unsubscribe',
	);
    
}
