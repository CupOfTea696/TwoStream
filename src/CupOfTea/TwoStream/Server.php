<?php namespace CupOfTea\TwoStream;

use App;
use ZMQ;

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\Wamp\WampServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Server\FlashPolicy;

use React\ZMQ\Context as ZMQContext;
use React\Socket\Server as ReactServer;
use React\EventLoop\Factory as EventLoopFactory;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use CupOfTea\TwoStream\Console\Output;
use CupOfTea\TwoStream\Server\Dispatcher;

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
    
	protected $output;
    
    protected $Dispatcher;
    
    protected $loop;
    
    protected $Ws;
    
    protected $server;
    
    protected $pull;
    
    public function __construct($Kernel){
        $this->output = new Output();
        $this->Dispatcher = new Dispatcher($Kernel, $this->output->level(3));
        
        parent::__construct();
    }
    
    public function fire(){
        ini_set('xdebug.var_display_max_depth', 4);
		$this->line('TwoStream listening on port ' . $this->option('port'));
        $this->create()->run();
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
                    new WampServer(
                        $this->Dispatcher
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
            $this->error('react/zmq dependency is required if push is enabled. Stopping server', 1);
            die();
        }
        
		$context = new ZMQContext($this->loop);
        
		$this->pull = $context->getSocket(ZMQ::SOCKET_PULL, self::SOCKET_PULL_ID . '.' . App::environment());
		$this->pull->bind('tcp://127.0.0.1:' . $this->option('push-port'));
		$this->pull->on('message', array($this->latchet, 'serverPublish')); // TODO
        
        $this->info('Push enabled', 1);
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
        
        $this->info('Flash connections allowed', 1);
	}
    
    /**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions(){
		return [
			['port', 'p', InputOption::VALUE_OPTIONAL, 'Port the WebSocket server should listen on', config('twostream.websocket.port')],
            ['push', null, InputOption::VALUE_OPTIONAL, 'Enable push messages from the server to the client', config('twostream.push.enabled')],
			['push-port', null, InputOption::VALUE_OPTIONAL, 'Port the push server should listen on', config('twostream.push.port')],
            ['flash', null, InputOption::VALUE_OPTIONAL, 'Allow legacy browsers to connect with the websocket polyfill', config('twostream.flash.allowed')],
			['flash-port', null, InputOption::VALUE_OPTIONAL, 'Port the push server should listen on', config('twostream.flash.port')],
        ];
	}
    
}
