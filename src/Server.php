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

use CupOfTea\TwoStream\Console\Output;
use CupOfTea\TwoStream\Console\Command;
use CupOfTea\TwoStream\Server\Dispatcher;
use CupOfTea\TwoStream\Contracts\Ws\Kernel;
use CupOfTea\TwoStream\Events\ServerStarted;

use Illuminate\Session\SessionManager;
use Illuminate\Console\AppNamespaceDetectorTrait as AppNamespaceDetector;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Server extends Command
{
    
    use AppNamespaceDetector;
    
    /**
     * Listen IP
     *
     * @const string
     */
    const IP = '0.0.0.0';
    
    /**
     * Socket Pull ID
     *
     * @const string
     */
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
    
    /**
     * The Application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;
    
    /**
     * The Application Output.
     *
     * @var \CupOfTea\TwoStream\Console\Output
     */
    protected $out;
    
    /**
     * The Application Kernel
     *
     * @var \CupOfTea\TwoStream\Contracts\Ws\Kernel
     */
    protected $Kernel;
    
    /**
     * The WebSocket server Dispatcher
     *
     * @var \CupOfTea\TwoStream\Server\Dispatcher
     */
    protected $Dispatcher;
    
    /**
     * The loop
     *
     * @var \React\EventLoop\LibEventLoop
     */
    protected $loop;
    
    /**
     * The WebSocket server.
     *
     * @var \React\Socket\Server
     */
    protected $Ws;
    
    /**
     * The IO Server
     *
     * @var \Ratchet\Server\IoServer
     */
    protected $server;
    
    /**
     * ZMQ Pull Socket.
     *
     * @var \ZMQSocket
     */
    protected $pull;
    
    /**
     * Create a new server command instance.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    public function __construct($app)
    {
        parent::__construct();
        
        $this->app = $app;
        $this->out = new Output();
    }
    
    /**
     * Fire the command
     *
     * @return void
     */
    public function fire()
    {
        if (!$this->isInstalled()) {
            return $this->error('TwoStream is not installed. Please run twostream:install before attempting to run this command.');
        }
        
        $this->line('TwoStream Server listening on IP <comment>[' . self::IP . ']</comment> with port <comment>[' . $this->option('port') . ']</comment>');
        event(new ServerStarted($this->option('port')));
        $this->boot();
        
        $this->start();
    }
    
    /**
     * Start the WAMP Server
     *
     * @return void
     */
    protected function start()
    {
        $this->loop->run();
    }
    
    /**
     * Build the WAMP Server
     *
     * @return \React\EventLoop\LibEventLoop
     */
    protected function boot()
    {
        $this->buildDispatcher();
        $this->createLoop();
        
        if ($this->option('push')) {
            $this->enablePush();
        }
        
        $this->bootWsServer();
        $this->bootHttpServer();
        
        if ($this->option('flash')) {
            $this->allowFlash();
        }
        
        return $this->loop;
    }
    
    /**
     * Get the Session Driver
     *
     * @return \Illuminate\Session\SessionInterface
     */
    protected function getSession()
    {
        return with(new SessionManager($this->app))->driver();
    }
    
    /**
     * Build the Server's Kernel
     *
     * @return \CupOfTea\TwoStream\Foundation\Ws\Kernel
     */
    protected function buildKernel()
    {
        $this->app->singleton(
            Kernel::class,
            $this->getAppNamespace() . 'Ws\Kernel'
        );
        
        return $this->Kernel = $this->app->make(Kernel::class);
    }
    
    /**
     * Build the Server's Dispatcher
     *
     * @return \CupOfTea\TwoStream\Server\Dispatcher
     */
    protected function buildDispatcher()
    {
        $this->app->singleton(
            'Ratchet\Wamp\WampServerInterface',
            Dispatcher::class
        );
        
        return $this->Dispatcher = $this->app->make('Ratchet\Wamp\WampServerInterface', [$this->getSession(), $this->buildKernel(), clone $this->out]);
    }
    
    /**
     * Create the Server's EventLoop
     *
     * @return \React\EventLoop\LibEventLoop
     */
    protected function createLoop()
    {
        return $this->loop = EventLoopFactory::create();
    }
    
    /**
     * Build the WebSocket Server
     *
     * @return React\Socket\Server
     */
    protected function bootWsServer()
    {
        $this->ws = new ReactServer($this->loop);
        $this->ws->listen($this->option('port'), self::IP);
        
        return $this->ws;
    }
    
    /**
     * Build the Http Server
     *
     * @return React\Socket\Server
     */
    protected function bootHttpServer()
    {
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
    protected function enablePush()
    {
        if (!class_exists('\React\ZMQ\Context')) {
            $this->error('React/ZMQ dependency is required to enable push. Stopping server.', 1);
            die();
        }
        
        $context = new ZMQContext($this->loop);
        
        $this->pull = $context->getSocket(ZMQ::SOCKET_PULL, self::SOCKET_PULL_ID . '.' . App::environment());
        $this->pull->bind('tcp://127.0.0.1:' . $this->option('push-port'));
        $this->pull->on('message', [$this->Dispatcher, 'push']);
        
        $this->info('Push enabled on port <comment>[' . $this->option('push-port') . ']</comment>', 1);
    }
    
    /**
     * Allow Flash sockets to connect to our server.
     * For this we have to listen on flash.port (843) and return
     * the flashpolicy
     *
     * @return void
     */
    protected function allowFlash()
    {
        $flashSock = new ReactServer($this->loop);
        $flashSock->listen($this->option('flash-port'), self::IP);
        
        $policy = new FlashPolicy;
        $policy->addAllowedAccess('*', $this->option('port'));
        
        $webServer = new IoServer($policy, $flashSock);
        
        $this->info('Flash connections allowed with policy on port <comment>[' . $this->option('flash-port') . ']</comment>', 1);
    }
    
    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['port', 'p', InputOption::VALUE_OPTIONAL, 'Port the WebSocket server should listen on', config('twostream.websocket.port')],
            ['push', 'z', InputOption::VALUE_OPTIONAL, 'Enable push messages from the server to the client', config('twostream.push.enabled')],
            ['push-port', 'Z', InputOption::VALUE_OPTIONAL, 'Port the push server should listen on', config('twostream.push.port')],
            ['flash', 'f', InputOption::VALUE_OPTIONAL, 'Allow legacy browsers to connect with the websocket polyfill', config('twostream.flash.allowed')],
            ['flash-port', 'F', InputOption::VALUE_OPTIONAL, 'Port the push server should listen on', config('twostream.flash.port')],
        ];
    }
    
    /**
     * Check if the TwoStream Package is installed
     *
     * @return bool
     */
    protected function isInstalled()
    {
        $disk = Storage::createLocalDriver([
            'driver' => 'local',
            'root'   => app_path(),
        ]);
        
        foreach (TwoStreamServiceProvider::pathsToPublish(strtolower(TwoStream::PACKAGE), 'required') as $required) {
            if (!$disk->exists(str_replace(['.stub', app_path()], ['.php', ''], $required))) {
                return false;
            }
        }
        
        return true;
    }
    
}
