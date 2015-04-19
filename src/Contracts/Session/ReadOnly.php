<?php namespace CupOfTea\TwoStream\Contracts\Session;

interface ReadOnly {

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
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

}
