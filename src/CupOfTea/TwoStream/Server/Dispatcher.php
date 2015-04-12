<?php namespace CupOfTea\TwoStream\Server;

use Crypt;
use Session;
use Exception;

use Illuminate\Http\Request;

use Ratchet\ConnectionInterface as Connection;
use Ratchet\Wamp\WampServerInterface as DispatcherContract;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Dispatcher implements DispatcherContract{
    
    const WS_VERB_CALL          = 'CALL';
    const WS_VERB_PUBLISH       = 'PUBLISH';
    const WS_VERB_SUBSCRIBE     = 'SUBSCRIBE';
    const WS_VERB_UNSUBSCRIBE   = 'UNSUBSCRIBE';
    
    protected $Kernel;
    
    protected $output;
    
    /**
	 * Create a new Dispatcher instance.
	 *
	 * @return void
	 */
	public function __construct($Kernel, $output){
        $this->Kernel = $Kernel;
        $this->output = $output;
	}
    
    /**
     * Route Topic events to Controller
     *
     */
    
    /**
     * @inheritdoc
     */
    public function onCall(Connection $connection, $id, $topic, array $params) {
        // Pass through router.
        
        // default reaction if route not set
        $connection->callError($id, $topic, 'RPC not supported.');
    }
    
    /**
     * @inheritdoc
     */
    public function onPublish(Connection $connection, $topic, $event, array $exclude, array $eligible) {
        $this->output->writeln(json_encode($topic->getId()));
        $this->output->writeln(json_encode($event));
        $this->output->writeln(json_encode($exclude));
        $this->output->writeln(json_encode($eligible));
        
        $request = $this->buildRequest($connection, $topic, $event, 'PUBLISH');
        
        $response = $this->Kernel->handle($request);
        
        $topic->broadcast($response->getContent());
    }
    
    /**
     * @inheritdoc
     */
    public function onSubscribe(Connection $connection, $topic) {
        // Pass through router.
    }
    
    /**
     * @inheritdoc
     */
    public function onUnSubscribe(Connection $connection, $topic) {
        // Pass through router.
    }
    
    /**
     * Handle Connection events
     *
     */
    
    /**
     * @inheritdoc
     */
    public function onOpen(Connection $connection) {
        $connection->Session = Session::getFacadeRoot()->driver();
        if($sessionId = $this->getSessionCookie($connection))
            $connection->Session->setId($sessionId);
        
        $this->output->writeln("<info>Connection from <comment>[{$connection->Session->getId()}]</comment> opened.</info>");
    }
    
    /**
     * @inheritdoc
     */
    public function onClose(Connection $connection) {
        $this->output->writeln("<info>Connection from <comment>[{$connection->Session->getId()}]</comment> closed.</info>");
    }
    
    /**
     * @inheritdoc
     */
    public function onError(Connection $connection, Exception $e) {
        $this->output->writeln("<error>Error: {$e->getMessage()}</error>");
    }
    
    protected function buildRequest($connection, $topic, $data, $verb){
        return Request::createFromBase(
            SymfonyRequest::create(
                'ws://' . $connection->WebSocket->request->getHost() . ':' . config('twostream.websocket.port') . '/' . trim($topic->getId(), '/'),
                strtoupper($verb),
                ['data' => $data,], // params
                [], // cookies
                [], // files
                [], // server
                null // content
            )
        );
    }
    
    protected function getSessionCookie(Connection $connection){
        $cookie = urldecode($connection->WebSocket->request->getCookie(config('session.cookie')));
        
        return $cookie ? Crypt::decrypt($cookie) : false;
    }
}
