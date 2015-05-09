<?php namespace CupOfTea\Package;

trait Package
{
    
    /**
     * Package Info
     *
     */
    
    /**
     * Package Info
     *
     * @return string
     */
    public function package($info = false)
    {
        // Retrieve package in dot notation.
        if ($info == 'dot')
            return strtolower(str_replace('/', '.', self::PACKAGE));
        
        // Include version
        if ($info == 'v')
            return self::PACKAGE . ':' . self::VERSION;
        
        return self::PACKAGE;
    }
    
    /**
     * Package Version
     *
     * @return string
     */
    public function version()
    {
        return self::VERSION;
    }
    
}
