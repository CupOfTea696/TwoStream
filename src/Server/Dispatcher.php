<?php namespace CupOfTea\TwoStream\Server;

use Auth;
use Crypt;
use Session;
use Exception;
use WsSession;
use CupOfTea\Chain\Chain;
use CupOfTea\TwoStream\Console\Output;
use CupOfTea\TwoStream\Console\Writer;
use CupOfTea\TwoStream\Session\ReadOnly;
use CupOfTea\TwoStream\Events\ServerStopped;
use CupOfTea\TwoStream\Events\ClientConnected;
use CupOfTea\TwoStream\Events\ClientDisconnected;
use CupOfTea\TwoStream\Contracts\Exceptions\Handler;
use CupOfTea\TwoStream\Exceptions\InvalidRecipientException;
use CupOfTea\TwoStream\Wamp\TopicAccessTrait as TopicAccess;
use Illuminate\Http\Request;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Console\AppNamespaceDetectorTrait as AppNamespaceDetector;
use Ratchet\ConnectionInterface as Connection;
use Ratchet\Wamp\WampServerInterface as DispatcherContract;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Dispatcher implements DispatcherContract
{
    use AppNamespaceDetector, TopicAccess, Writer;
    
    const WAMP_VERB_CALL = 'CALL';
    const WAMP_VERB_PUBLISH = 'PUBLISH';
    const WAMP_VERB_SUBSCRIBE = 'SUBSCRIBE';
    const WAMP_VERB_UNSUBSCRIBE = 'UNSUBSCRIBE';
    
    protected $Session;
    
    protected $Kernel;
    
    protected $out;
    
    protected $topic;
    
    protected $users = [];
    
    /**
     * Create a new Dispatcher instance.
     *
     * @param $Session
     * @param $Kernel
     * @param \CupOfTea\TwoStream\Console\Output $output
     * @param \Illuminate\Contracts\Encryption\Encrypter $Encrypter
     * @return void
     */
    public function __construct($Session, $Kernel, Output $output, Encrypter $Encrypter)
    {
        $this->Session = $Session;
        $this->Kernel = $Kernel;
        $this->out = $output->level(2);
        $this->Encrypter = $Encrypter;
    }
    
    /**
     * Route Topic events to Controller.
     */
    
    /**
     * {@inheritdoc}
     */
    public function onCall(Connection $connection, $id, $topic, array $params)
    {
        $this->setTopic($topic, $id);
        
        $request = $this->buildRequest(self::WAMP_VERB_CALL, $connection, $topic, [], $params);
        $response = $this->handle($connection, $request);
        
        if ($response instanceof NotFoundHttpException) {
            $msg = config('twostream.response.rpc.enabled') ?
                config('twostream.response.rpc.error.enabled') : config('twostream.response.rpc.error.disabled');
            $connection->callError($id, 'wamp.error.no_such_procedure', $msg);
        } else {
            $error = array_get($response, 'error');
            
            if ($error) {
                $connection->callError($id, array_get($response, 'error.domain', $topic), array_get($response, 'error.msg', $error));
            } else {
                $connection->callResult($id, $response);
            }
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function onPublish(Connection $connection, $topic, $event, array $exclude, array $eligible)
    {
        $this->setTopic($topic);
        
        $event = json_decode($event);
        $json = json_decode($event);
        if (json_last_error() == JSON_ERROR_NONE) {
            $event = $json;
        }
        
        $request = $this->buildRequest(self::WAMP_VERB_PUBLISH, $connection, $topic, $event);
        $response = $this->handle($connection, $request);
        
        $this->send($response, $connection, $topic);
    }
    
    /**
     * {@inheritdoc}
     */
    public function onSubscribe(Connection $connection, $topic)
    {
        $this->setTopic($topic);
        
        $request = $this->buildRequest(self::WAMP_VERB_SUBSCRIBE, $connection, $topic);
        $response = $this->handle($connection, $request);
        
        $this->send($response, $connection, $topic);
    }
    
    /**
     * {@inheritdoc}
     */
    public function onUnSubscribe(Connection $connection, $topic)
    {
        $this->setTopic($topic);
        
        $request = $this->buildRequest(self::WAMP_VERB_UNSUBSCRIBE, $connection, $topic);
        $response = $this->handle($connection, $request);
        
        $this->send($response, $connection, $topic);
    }
    
    /**
     * Handle Push messages.
     *
     * @param string $message
     * @return void
     * @throws \CupOfTea\TwoStream\Exceptions\InvalidRecipientException
     */
    public function push($message)
    {
        $message = json_decode($message, true);
        $topic = array_get($this->getTopics(), $message['topic']);
        $data = $message['data'];
        $recipient = array_get($message, 'recipient');
        
        // Handle TwoStream::stop();
        if ($message['topic'] == 'cupoftea/twostream/server/stop') {
            if ($data['secret'] == config('app.key')) {
                event(new ServerStopped());
                $this->line('Stop Command fired.');
                $this->question('Stopping Server.');
                
                die();
            } else {
                // report malicious attempt
            }
        }
        
        if (! $topic) {
            return;
        }
        
        $this->setTopic($topic);
        
        if ($recipient == 'all') {
            $topic->broadcast($data);
        } else {
            foreach ((array) $recipient as $recipient) {
                if (($sessionId = $this->getUserSessionId($recipient)) || ($sessionId = WsSession::isValidId($recipient) ? $recipient : false)) {
                    foreach ($topic->getIterator() as $client) {
                        if ($client->session == $sessionId) {
                            $client->event($topic->getId(), $data);
                        }
                    }
                } else {
                    throw new InvalidRecipientException($recipient);
                }
            }
        }
    }
    
    /**
     * Handle Request.
     *
     * @param \Ratchet\ConnectionInterface $connection
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    protected function handle(Connection $connection, $request)
    {
        $this->loadSession($connection);
        
        return $this->Kernel->handle($request);
    }
    
    /**
     * Send Response.
     *
     * @param \Illuminate\Http\Response $response
     * @param \Ratchet\ConnectionInterface $connection
     * @param \Ratchet\Wamp\Topic $topic
     * @return void
     * @throws \CupOfTea\TwoStream\Exceptions\InvalidRecipientException
     */
    protected function send($response, Connection $connection, $topic)
    {
        if ($response instanceof NotFoundHttpException) {
            return;
        }
        
        $recipient = array_get($response, 'recipient', config('twostream.response.recipient'));
        $data = array_get($response, 'data', $response);
        
        if ($recipient == 'all') {
            $topic->broadcast($data);
        } elseif ($recipient == 'except') {
            foreach ($topic->getIterator() as $client) {
                if ($client->session != $connection->session) {
                    $client->event($topic->getId(), $data);
                }
            }
        } else {
            if ($recipient == 'requestee') {
                $recipient = $connection->session;
            }
            
            foreach ((array) $recipient as $recipient) {
                if (($sessionId = $this->getUserSessionId($recipient)) || ($sessionId = WsSession::isValidId($recipient) ? $recipient : false)) {
                    foreach ($topic->getIterator() as $client) {
                        if ($client->session == $sessionId) {
                            $client->event($topic->getId(), $data);
                        }
                    }
                } else {
                    throw new InvalidRecipientException($recipient);
                }
            }
        }
    }
    
    /**
     * Handle Connection events.
     */
    
    /**
     * {@inheritdoc}
     */
    public function onOpen(Connection $connection)
    {
        $sessionId = $this->getSessionIdFromCookie($connection);
        $this->loadSession($connection);
        
        if (($user = $this->getUser($connection)) !== false) {
            $this->users[$user] = $sessionId;
        }
        
        event(new ClientConnected($connection->session));
        $this->info("Connection from <comment>[{$connection->session}]</comment> opened.");
    }
    
    /**
     * {@inheritdoc}
     */
    public function onClose(Connection $connection)
    {
        $sessionId = $this->getSessionIdFromCookie($connection);
        
        event(new ClientDisconnected($sessionId));
        $this->info("Connection from <comment>[$sessionId]</comment> closed.");
    }
    
    /**
     * {@inheritdoc}
     */
    public function onError(Connection $connection, Exception $e)
    {
        $response = with(new Chain())
            ->requires(Handler::class)
            ->on(app($this->getAppNamespace() . 'Exceptions\WsHandler'))
            ->call('report', 'render')
            ->with($e)
            ->getResult('render');
        
        if ($response !== null) {
            $topic = $this->topic['topic'];
            
            if ($call_id = array_get($this->topic, 'call_id')) {
                $connection->callError($call_id, array_get($response, 'error.domain', $topic), array_get($response, 'error.msg', $response['error']));
            } else {
                $this->send(['recipient' => 'requestee', 'data' => $response], $connection, $topic);
            }
        }
    }
    
    /**
     * Load the Session data and store it into a Read-Only Session.
     *
     * @param \Ratchet\ConnectionInterface $connection
     * @return void
     */
    protected function loadSession(Connection $connection)
    {
        $sessionId = $connection->session = $this->getSessionIdFromCookie($connection);
        
        $session = clone $this->Session;
        $session->setId($sessionId);
        $session->start();
        
        $readonly = (new ReadOnly(config('session.cookie')))->initialize($session->all(), $sessionId);
        WsSession::swap($readonly);
        Session::flush();
        
        unset($session);
    }
    
    /**
     * Build an \Illuminate\Http\Request object.
     *
     * @param string $verb
     * @param \Ratchet\ConnectionInterface $connection
     * @param \Ratchet\Wamp\Topic $topic
     * @param array $data
     * @param array|null $params
     * @return \Illuminate\Http\Request
     */
    protected function buildRequest($verb, Connection $connection, $topic, $data = [], $params = null)
    {
        $uri = [
            'protocol'  => 'ws://',
            'host'      => $connection->WebSocket->request->getHost(),
            'port'      => ':' . config('twostream.websocket.port'),
            'path'      => '/' . trim($topic->getId(), '/') . (isset($params) ? '/' . implode('/', $params) : ''),
        ];
        $cookies = $connection->WebSocket->request->getCookies();
        array_forget($cookies, config('session.cookie')); // Make sure the normal Session Facade does not contain the client's Session.
        
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
    
    /**
     * Get the Session ID from the Cookie.
     *
     * @param \Ratchet\ConnectionInterface $connection
     * @return string
     */
    protected function getSessionIdFromCookie(Connection $connection)
    {
        $cookie = urldecode($connection->WebSocket->request->getCookie(config('session.cookie')));
        
        return $cookie ? Crypt::decrypt($cookie) : null;
    }
    
    /**
     * Get the current logged in user.
     *
     * @param \Ratchet\ConnectionInterface $connection
     * @return mixed
     */
    protected function getUser(Connection $connection)
    {
        $recaller = $connection->WebSocket->request->getCookie(Auth::getRecallerName());
        try {
            $recaller = $this->Encrypter->decrypt(urldecode($recaller));
        } catch (Exception $e) {
            $recaller = $this->Encrypter->decrypt($recaller);
        }
        
        $id = explode('|', $recaller, 2)[0];
        $model = config('auth.model');
        
        return $model::find($id) !== null ? $id : false;
    }
    
    /**
     * Get the Session ID for a connected user.
     *
     * @param $user
     * @return mixed
     */
    protected function getUserSessionId($user)
    {
        $model = config('auth.model');
        
        if ($user instanceof $model) {
            return array_get($this->users, $user->getKey(), false);
        } elseif ($model::find($user) !== null) {
            return array_get($this->users, $user, false);
        }
        
        return false;
    }
    
    public function setTopic($topic, $call_id = null)
    {
        if ($call_id === null) {
            $this->topic = ['topic' => $topic];
        } else {
            $this->topic = ['topic' => $topic, 'call_id' => $call_id];
        }
    }
}
