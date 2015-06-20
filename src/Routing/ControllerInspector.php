<?php namespace CupOfTea\TwoStream\Routing;

use Illuminate\Routing\ControllerInspector as LaravelControllerInspector;

class ControllerInspector extends LaravelControllerInspector
{
    
    /**
     * {@inheritdoc}
     */
    protected $verbs = [
		'call', 'publish', 'subscribe', 'unsubscribe',
	];
    
}
