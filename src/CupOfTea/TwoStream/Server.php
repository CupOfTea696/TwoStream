<?php namespace CupOfTea\TwoStream;

use App;
use ZMQ;
use Session;

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\Wamp\WampServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Server\FlashPolicy;
use Rachet\Session\SessionProvider;

use Illuminate\Console\Command;

use React\ZMQ\Context as ZMQContext;
use React\Socket\Server as ReactServer;
use React\EventLoop\Factory as EventLoopFactory;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Server extends Command{
    
    const IP = '0.0.0.0';
    
    const SOCKET_PULL_ID = 'twostream.pull';
    
    /**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'twostream:listen';
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Let the WebSocket server listen on specified port for incomming connections';
    
    protected $Dispatcher;
    
    /**
	 * This package's Server configuration
	 *
	 * @var array
	 */
    protected $cfg;
    
    protected $loop;
    
    protected $Ws;
    
    protected $server;
    
    protected $pull;
    
    public function __construct($app){
        $this->Dispatcher = $app->make('Dispatcher');
        $this->Session = Session::getFacadeRoot()->driver();
        
        parent::construct();
    }
    
    public function fire(){
        $this->create()->run();
		$this->info('TwoStream listening on port ' . $this->option('port'));
    }
    
    protected function create(){
        $this->loop = EventLoopFactory::create();
        
        if($this->option('push'))
            $this->enablePush();
        
        $this->ws = new ReactServer($this->loop);
        $this->ws->listen($this->option('port'), self::IP);
        
        $this->server = new IoServer(
            new HttpServer(
                new WsServer(
                    new SessionProvider(
                        new WampServer(
                            $this->Dispatcher
                        ), $this->Session, [
                            'key' => $this->cfg['session_name']
                        ]
                    )
                )
            ), $this->ws
        );
        
        if($this->option('flash'))
            $this->allowFlash();
        
        return $this->loop;
    }
    
	/**
	 * Enable the option to push messages from
	 * the Server to the client
	 * 
	 * @return void
	 */
	protected function enablePush(){
        if(!class_exists('\React\ZMQ\Context')){
            $this->error('react/zmq dependency is required if push is enabled');
            die();
        }
        
		$context = new ZMQContext($this->loop);
        
		$this->pull = $context->getSocket(ZMQ::SOCKET_PULL, self::SOCKET_PULL_ID . '.' . App::environment());
		$this->pull->bind('tcp://127.0.0.1:' . $this->option('push-port'));
		$this->pull->on('message', array($this->latchet, 'serverPublish')); // TODO
        
		$this->info('Push enabled');
	}
    
    /**
	 * Allow Flash sockets to connect to our server.
	 * For this we have to listen on flash.port (843) and return
	 * the flashpolicy
	 * 
	 * @return void
	 */
	protected function allowFlash(){
		$flashSock = new ReactServer($this->loop);
		$flashSock->listen($this->option('flash-port'), self::IP);
        
		$policy = new FlashPolicy;
		$policy->addAllowedAccess('*', $this->option('port'));
        
		$webServer = new IoServer($policy, $flashSock);
        
		$this->info('Flash connection allowed');
	}
    
    /**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions(){
		return [
			['port', 'p', InputOption::OPTIONAL, 'Port the WebSocket server should listen on', config('twostream.websocket.port')],
            ['push', null, InputOption::VALUE_NONE, 'Enable push messages from the server to the client', config('twostream.push.enabled')],
			['push-port', null, InputOption::OPTIONAL, 'Port the push server should listen on', config('twostream.push.port')],
            ['flash', null, InputOption::VALUE_NONE, 'Allow legacy browsers to connect with the websocket polyfill', config('twostream.flash.allowed')],
			['flash-port', null, InputOption::OPTIONAL, 'Port the push server should listen on', config('twostream.flash.port')],
        ];
	}
    
}
