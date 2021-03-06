<?php namespace CupOfTea\TwoStream\Session;

use CupOfTea\TwoStream\Contracts\Session\ReadOnly as ReadOnlyContract;

class ReadOnly implements ReadOnlyContract
{
    /**
     * The session attributes.
     *
     * @var array
     */
    protected $attributes = [];
    
    /**
     * Session store started status.
     *
     * @var bool
     */
    protected $started = false;
    
    /**
     * Create a new session instance.
     *
     * @param  string $name
     * @param  string|null $id
     * @return void
     */
    public function __construct($name, $id = null)
    {
        $this->id = $id;
        $this->name = $name;
    }
    
    /**
     * Store the Session data in the ReadOnly Session and lock it.
     *
     * @param  array $attributes
     * @param  string|null $id
     * @return void
     */
    public function initialize($attributes, $id = null)
    {
        if ($this->started) {
            return $this;
        }
        
        $this->id = $id ?: $this->id;
        $this->attributes = $attributes;
        $this->started = true;
        
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Determine if this is a valid session ID.
     *
     * @param  string  $id
     * @return bool
     */
    public function isValidId($id)
    {
        return is_string($id) && preg_match('/^[a-f0-9]{40}$/', $id);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * {@inheritdoc}
     */
    public function has($name)
    {
        return ! is_null($this->get($name));
    }
    
    /**
     * {@inheritdoc}
     */
    public function get($name, $default = null)
    {
        return array_get($this->attributes, $name, $default);
    }
    
    /**
     * Determine if the session contains old input.
     *
     * @param  string|null  $key
     * @return bool
     */
    public function hasOldInput($key = null)
    {
        $old = $this->getOldInput($key);
        
        return is_null($key) ? count($old) > 0 : ! is_null($old);
    }
    
    /**
     * Get the requested item from the flashed input array.
     *
     * @param  string|null  $key
     * @param  mixed   $default
     * @return mixed
     */
    public function getOldInput($key = null, $default = null)
    {
        $input = $this->get('_old_input', []);
        
        // Input that is flashed to the session can be easily retrieved by the
        // developer, making repopulating old forms and the like much more
        // convenient, since the request's previous input is available.
        return array_get($input, $key, $default);
    }
    
    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->attributes;
    }
    
    /**
     * {@inheritdoc}
     */
    public function isStarted()
    {
        return true;
    }
    
    /**
     * Get the CSRF token value.
     *
     * @return string
     */
    public function token()
    {
        return $this->get('_token');
    }
    
    /**
     * Get the CSRF token value.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token();
    }
    
    /**
     * Get the previous URL from the session.
     *
     * @return string|null
     */
    public function previousUrl()
    {
        return $this->get('_previous.url');
    }
}
