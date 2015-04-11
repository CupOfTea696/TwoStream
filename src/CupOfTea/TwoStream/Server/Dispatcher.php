<?php namespace CupOfTea\TwoStream\Server;

use Crypt;
use Session;
use Exception;

use Ratchet\Wamp\WampServerInterface as DispatcherContract;
use Ratchet\ConnectionInterface as Connection;

class Dispatcher implements DispatcherContract{
    
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
    public function onPublish(Connection $connection, $topic, $event, array $exclude, array $eligible) {
        $topic->broadcast($event);
        $this->output->writeln(json_encode($connection));
        $this->output->writeln(json_encode($topic->getId()));
        $this->output->writeln(json_encode($event));
        $this->output->writeln(json_encode($exclude));
        $this->output->writeln(json_encode($eligible));
        return;
        
        $request = false;
        
        $this->Kernel->handle($request);
    }
    
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
        
        $this->output->writeln("<info>Connection from <comment>[$sessionId]</comment> opened.</info>");
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
    
    protected function getSessionCookie(Connection $connection){
        $cookie = urldecode($connection->WebSocket->request->getCookie(config('session.cookie')));
        
        return $cookie ? Crypt::decrypt($cookie) : false;
    }
}
