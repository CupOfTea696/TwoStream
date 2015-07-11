<?php namespace CupOfTea\TwoStream\Contracts\Session;

interface ReadOnly
{
    
    /**
     * Get the session ID.
     *
     * @return int
     */
    public function getId();
    
    /**
     * Get the session name.
     *
     * @return string
     */
    public function getName();
    
    /**
     * Determine if a session key has been set.
     *
     * @return bool
     */
    public function has($name);
    
    /**
     * Get a session value.
     *
     * @param $name
     * @param null $default
     * @return mixed
     */
    public function get($name, $default = null);
    
    /**
     * Get all session values.
     *
     * @return array
     */
    public function all();
    
    /**
     * Determine if the session has started.
     *
     * @return bool
     */
    public function isStarted();
    
}
