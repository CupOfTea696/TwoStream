<?php namespace CupOfTea\TwoStream\Server;

use Crypt;
use Session;
use Exception;
use WsSession;

use Illuminate\Http\Request;

use Ratchet\ConnectionInterface as Connection;
use Ratchet\Wamp\WampServerInterface as DispatcherContract;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Dispatcher implements DispatcherContract{
    
    const WS_VERB_CALL          = 'CALL';
    const WS_VERB_PUBLISH       = 'PUBLISH';
    const WS_VERB_SUBSCRIBE     = 'SUBSCRIBE';
    const WS_VERB_UNSUBSCRIBE   = 'UNSUBSCRIBE';
    
    protected $Session;
    
    protected $Kernel;
    
    protected $output;
    
    /**
	 * Create a new Dispatcher instance.
	 *
	 * @return void
	 */
	public function __construct($Session, $Kernel, $output){
        $this->Session = $Session;
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
        $request = $this->buildRequest(self::WS_VERB_CALL, $connection, $topic, $params);
        $response = $this->Kernel->handle($request);
        
        // default reaction if route not set
        $connection->callError($id, $topic, 'RPC not supported.');
    }
    
    /**
     * @inheritdoc
     */
    public function onPublish(Connection $connection, $topic, $event, array $exclude, array $eligible) {
        $request = $this->buildRequest(self::WS_VERB_PUBLISH, $connection, $topic, $event);
        $response = $this->Kernel->handle($request);
        
        $this->send($response, $connection, $topic);
    }
    
    /**
     * @inheritdoc
     */
    public function onSubscribe(Connection $connection, $topic) {
        $request = $this->buildRequest(self::WS_VERB_SUBSCRIBE, $connection, $topic);
        $response = $this->Kernel->handle($request);
        
        $this->send($response, $connection, $topic);
    }
    
    /**
     * @inheritdoc
     */
    public function onUnSubscribe(Connection $connection, $topic) {
        $request = $this->buildRequest(self::WS_VERB_UNSUBSCRIBE, $connection, $topic);
        $response = $this->Kernel->handle($request);
        
        $this->send($response, $connection, $topic);
    }
    
    /**
     * Send Response
     *
     */
    protected function send($response, Connection $connection, $topic){
        if(!$content = $response->getContent())
            return;
        
        $content = (array)json_decode($content, true);
         = array_get($content, 'send_to', config('request.send_to'));
        $data = array_get($content, 'data', count($content) ? $content : $content[0]);
        
        if($recipient == 'all'){
            $topic->broadcast($data);
        }elseif($recipient == 'except'){
            foreach($topic->getIterator() as $client){
                if($client->Session->getId() != $connection->Session->getId())
                    $client->event($topic->getId(), $data);
            }
        }else{
            if($recipient == 'requestee')
                $recipient = $connection->Session->getId();
            
            foreach((array)$recipient as $recipient){
                // TODO: if translateUserToSessionId || 
                if(WsSession::isValidId($recipient)){
                    foreach($topic->getIterator() as $client){
                        if($client->Session->getId() == $recipient)
                            $client->event($topic->getId(), $data);
                    }
                }else{
                    throw new InvalidRecipientException($recipient);
                }
            }
        }
    }
    
    /**
     * Handle Connection events
     *
     */
    
    /**
     * @inheritdoc
     */
    public function onOpen(Connection $connection) {
        $this->loadSession($connection);
        
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
    
    protected function loadSession(Connection $connection){
        $session = clone $this->Session;
        $session->setId($this->getSessionCookie($connection));
        $session->start();
        
        $connection->Session = WsSession::initialize($session->all(), $session->getId());
        unset($session);
    }
    
    protected function buildRequest($verb, $connection, $topic, $data = []){
        $cookies = $connection->WebSocket->request->getCookies();
        array_forget($cookies, config('session.cookie')); // Make sure the normal Session Facade does not contain the client's Session.
        
        return Request::createFromBase(
            SymfonyRequest::create(
                'ws://' . $connection->WebSocket->request->getHost() . ':' . config('twostream.websocket.port') . '/' . trim($topic->getId(), '/'),
                strtoupper($verb),
                ['topic' => $topic, 'data' => $data,], // params
                $cookies, // cookies
                [], // files
                [], // server
                null // content
            )
        );
    }
    
    protected function getSessionCookie(Connection $connection){
        $cookie = urldecode($connection->WebSocket->request->getCookie(config('session.cookie')));
        
        return $cookie ? Crypt::decrypt($cookie) : null;
    }
}
