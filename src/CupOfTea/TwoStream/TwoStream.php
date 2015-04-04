<?php namespace CupOfTea\TwoStream;


use CupOfTea\TwoStream\Contracts\Provider as ProviderContract;

class TwoStream implements ProviderContract{
    
    const PACKAGE = 'CupOfTea/TwoStream';
    const VERSION = '0.0.1-alpha';
    
	/**
	 * This package's configuration
	 *
	 * @var string
	 */
	protected $cfg;
    
    /**
	 * Create a new provider instance.
	 * 
	 * @param  string  $cfg
	 * @return void
	 */
	public function __construct($cfg)
	{
        $this->cfg = $cfg;
	}
    
    /**
     * Package Info
     *
     */
    
    /**
     * Package Info
     *
     * @return string
     */
    public function package($info = false){
        if($info == 'dot')
            return strtolower(str_replace('/', '.', self::PACKAGE));
        
        if($info == 'v')
            return self::PACKAGE . '/' . self::VERSION;
        
        return self::PACKAGE;
    }
    
    /**
     * Package Version
     *
     * @return string
     */
    public function version(){
        return self::VERSION;
    }
}