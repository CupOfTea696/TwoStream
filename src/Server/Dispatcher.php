<?php namespace CupOfTea\TwoStream\Server;

use Crypt;
use Session;
use Exception;
use WsSession;

use Illuminate\Http\Request;

use CupOfTea\TwoStream\Session\ReadOnly;

use Ratchet\ConnectionInterface as Connection;
use Ratchet\Wamp\WampServerInterface as DispatcherContract;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Dispatcher implements DispatcherContract{
    
    const WAMP_VERB_CALL        = 'CALL';
    const WAMP_VERB_PUBLISH     = 'PUBLISH';
    const WAMP_VERB_SUBSCRIBE   = 'SUBSCRIBE';
    const WAMP_VERB_UNSUBSCRIBE = 'UNSUBSCRIBE';
    
    protected $Session;
    
    protected $Kernel;
    
    protected $output;
    
    protected $sessions = [];
    
    /**
     * Create a new Dispatcher instance.
     *
     * @return void
     */
    public function __construct($Session, $Kernel, $output){
        $this->Session = $Session;
        $this->Kernel = $Kernel;
        $this->output = $output->level(2);
    }
    
    /**
     * Route Topic events to Controller
     *
     */
    
    /**
     * @inheritdoc
     */
    public function onCall(Connection $connection, $id, $topic, array $params) {
        $request = $this->buildRequest(self::WAMP_VERB_CALL, $connection, $topic, [], $params);
        $response = $this->handle($connection, $request);
        
        if($response->getStatusCode() == 404){
            $msg = config('twostream.response.rpc.enabled') ?
                config('twostream.response.rpc.error.enabled') : config('twostream.response.rpc.error.disabled');
            $connection->callError($id, 'wamp.error.no_such_procedure', $msg);
        }else{
            $content = (array)json_decode($response->getContent(), true);
            $error = array_get($content, 'error');
            
            if($error)
                $connection->callError($id, array_get($content, 'error.domain', $topic), array_get($content, 'error.msg', $error));
            else
                $connection->callResult($id, $content);
        }
    }
    
    /**
     * @inheritdoc
     */
    public function onPublish(Connection $connection, $topic, $event, array $exclude, array $eligible) {
        $request = $this->buildRequest(self::WAMP_VERB_PUBLISH, $connection, $topic, $event);
        $response = $this->handle($connection, $request);
        
        $this->send($response, $connection, $topic);
    }
    
    /**
     * @inheritdoc
     */
    public function onSubscribe(Connection $connection, $topic) {
        $request = $this->buildRequest(self::WAMP_VERB_SUBSCRIBE, $connection, $topic);
        $response = $this->handle($connection, $request);
        
        $this->send($response, $connection, $topic);
    }
    
    /**
     * @inheritdoc
     */
    public function onUnSubscribe(Connection $connection, $topic) {
        $request = $this->buildRequest(self::WAMP_VERB_UNSUBSCRIBE, $connection, $topic);
        $response = $this->handle($connection, $request);
        
        $this->send($response, $connection, $topic);
    }
    
    /**
     * Handle Request
     *
     */
    protected function handle(Connection $connection, $request){
        $this->loadSession($this->getSessionIdFromCookie($connection));
        
        return $this->Kernel->handle($request);
    }
    
    /**
     * Send Response
     *
     */
    protected function send($response, Connection $connection, $topic){
        if($response->getStatusCode() == 404 || !$content = $response->getContent())
            return;
        
        $content = (array)json_decode($content, true);
        $recipient = array_get($content, 'recipient', config('twostream.request.recipient'));
        $data = array_get($content, 'data', count($content) ? $content : $content[0]);
        $topic->broadcast($data);
        
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
        $sessionId = $this->getSessionIdFromCookie($connection);
        $this->loadSession($this->getSessionIdFromCookie($connection));
        
        $this->output->writeln("<info>Connection from <comment>[$sessionId]</comment> opened.</info>");
    }
    
    /**
     * @inheritdoc
     */
    public function onClose(Connection $connection) {
        $sessionId = $this->getSessionIdFromCookie($connection);
        $this->forgetSession($sessionId);
        
        $this->output->writeln("<info>Connection from <comment>[$sessionId]</comment> closed.</info>");
    }
    
    /**
     * @inheritdoc
     */
    public function onError(Connection $connection, Exception $e) {
        $this->output->writeln("<error>Error: {$e->getMessage()}</error>");
    }
    
    protected function loadSession($sessionId){
        if(array_get($this->sessions, $sessionId))
            return WsSession::swap($this->sessions[$sessionId]);
        
        $session = clone $this->Session;
        $session->setId($sessionId);
        $session->start();
        
        $this->sessions[$sessionId] = (new ReadOnly(config('session.cookie')))->initialize($session->all(), $sessionId);
        WsSession::swap($this->sessions[$sessionId]);
        unset($session);
    }
    
    protected function forgetSession($sessionId){
        array_forget($this->sessions, $sessionId);
    }
    
    protected function buildRequest($verb, $connection, $topic, $data = [], $params = null){
        $uri = [
            'protocol'  => 'ws://',
            'host'      => $connection->WebSocket->request->getHost(),
            'port'      => ':' . config('twostream.websocket.port'),
            'path'      => '/' . trim($topic->getId(), '/') . (isset($params) ? '/' . implode('/', $params) : ''),
        ];
        $cookies = $connection->WebSocket->request->getCookies();
        array_forget($cookies, config('session.cookie')); // Make sure the normal Session Facade does not contain the client's Session.
        
        echo var_dump(implode($uri));
        
        return Request::createFromBase(
            SymfonyRequest::create(
                implode($uri),
                strtoupper($verb),
                ['data' => $data], // params
                $cookies, // cookies
                [], // files
                [], // server
                null // content
            )
        );
    }
    
    protected function getSessionIdFromCookie(Connection $connection){
        $cookie = urldecode($connection->WebSocket->request->getCookie(config('session.cookie')));
        
        return $cookie ? Crypt::decrypt($cookie) : null;
    }
}
