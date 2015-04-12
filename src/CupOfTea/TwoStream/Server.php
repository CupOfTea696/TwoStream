<?php namespace CupOfTea\TwoStream;

use App;
use ZMQ;
use Storage;

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

use Illuminate\Console\AppNamespaceDetectorTrait as AppNamespaceDetector;

class Server extends Command{
    
    use AppNamespaceDetector;
    
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
    
	protected $app;
    
	protected $out;
    
    protected $Kernel;
    
    protected $Dispatcher;
    
    protected $loop;
    
    protected $Ws;
    
    protected $server;
    
    protected $pull;
    
    public function __construct($app){
        parent::__construct();
        
        $this->app = $app;
        $this->out = new Output();
    }
    
    public function fire(){
        if(!$this->isInstalled())
            return $this->error('TwoStream is not installed. Please run twostream:install before attempting to run this command.');
        
        $this->line('TwoStream Server listening on port <comment>[' . $this->option('port') . ']</comment>');
        $this->boot();
        $this->start();
    }
    
    protected function start(){
        $this->loop->run();
    }
    
    protected function boot(){
        $this->buildDispatcher();
        $this->createLoop();
        
        if($this->option('push'))
            $this->enablePush();
        
        $this->bootWsServer();
        $this->bootHttpServer();
        
        if($this->option('flash'))
            $this->allowFlash();
        
        return $this->loop;
    }
    
    protected function buildKernel(){
        $this->app->singleton(
            'CupOfTea\TwoStream\Contracts\Ws\Kernel',
            $this->getAppNamespace() . 'Ws\Kernel'
        );
        
        return $this->Kernel = $this->app->make('CupOfTea\TwoStream\Contracts\Ws\Kernel');
    }
    
    protected function buildDispatcher(){
        return $this->Dispatcher = new Dispatcher($this->buildKernel(), $this->out->level(3));
    }
    
    protected function createLoop(){
        return $this->loop = EventLoopFactory::create();
    }
    
    protected function bootWsServer(){
        $this->ws = new ReactServer($this->loop);
        $this->ws->listen($this->option('port'), self::IP);
        
        return $this->ws;
    }
    
    protected function bootHttpServer(){
        return $this->server = new IoServer(
            new HttpServer(
                new WsServer(
                    new WampServer(
                        $this->Dispatcher
                    )
                )
            ), $this->ws
        );
    }
    
	/**
	 * Enable the option to push messages from
	 * the Server to the client
	 *
	 * @return void
	 */
	protected function enablePush(){
        if(!class_exists('\React\ZMQ\Context')){
            $this->error('React/ZMQ dependency is required to enable push. Stopping server.', 1);
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
    
    protected function isInstalled(){
        $disk = Storage::createLocalDriver([
            'driver' => 'local',
			'root'   => app_path(),
        ]);
        
        foreach(TwoStreamServiceProvider::pathsToPublish(strtolower(TwoStream::PACKAGE), 'required') as $required){
            if(!$disk->exists(str_replace(['.stub', app_path()], ['.php', ''], $required)))
                return false;
        }
        
        return true;
    }
    
}
