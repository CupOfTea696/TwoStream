<?php namespace CupOfTea\TwoStream;

use ZMQ;
use Route;
use Request;
use ZMQContext;

use CupOfTea\TwoStream\Contracts\Provider as ProviderContract;

class TwoStream implements ProviderContract, MessageComponentInterface{
    
    /**
     * Package Info
     *
     */
    const PACKAGE = 'CupOfTea/TwoStream';
    const VERSION = '0.0.1-alpha-patch.1';
    
	/**
	 * This package's configuration
	 *
	 * @var array
	 */
	protected $cfg;
    
    /**
	 * Create a new provider instance.
	 *
	 * @param  string  $cfg
	 * @return void
	 */
	public function __construct($cfg){
        $this->cfg = $cfg;
	}
    
    /**
	 * Push a message to a client
	 * This function get's fired e.g after a ajax request and not
	 * after a websocket request. Because of that we don't have access
	 * to all the connections and there for have to connect to the
	 * latchet/ratchet server
	 *
	 * @param string $channel
	 * @param array $message
	 * @return void
	 */
	public function push($channel, $message){
		if(!$this->enablePush){
			throw new LatchetException("Publish not allowed.");
		}
		$message = array_merge(array('topic' => $channel), $message);
		$this->getSocket()->send(json_encode($message));
	}
	/**
	 * get zmqSocket to push messages
	 *
	 * @return ZMQSocket instance
	 */
	protected function getSocket(){
		//we don't have to connect the socket
		//for every new message sent
		if(isset($this->socket))
		{
			return $this->socket;
		}
		else
		{
			return $this->connectZmq();
		}
	}
	/**
	 * Connect to socket
	 *
	 * @return ZMQSocket instance
	 */
	protected function connectZmq()
	{
		$context = new ZMQContext();
		$this->socket = $context->getSocket(ZMQ::SOCKET_PUSH, Config::get('latchet::socketPushId', sprintf('latchet.push.%s', App::environment())));
		$this->socket->connect("tcp://localhost:".Config::get('latchet::zmqPort'));
		return $this->socket;
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
