<?php namespace CupOfTea\TwoStream;

use Exception;
use Ratchet\Wamp\WampServerInterface as DispatcherContract;
use Ratchet\ConnectionInterface as Connection;

class Dispatcher implements DispatcherContract{
    
    /**
	 * Create a new Dispatcher instance.
	 * 
	 * @return void
	 */
	public function __construct(){
        
	}
    
    /**
     * Route Topic events to Controller
     *
     */
    
    /**
     * @inheritdoc
     */
    public function onPublish(Connection $connection, $topic, $event, array $exclude, array $eligible) {
        echo json_encode($topic);
        // Pass through router.
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
        
    }
    
    /**
     * @inheritdoc
     */
    public function onClose(Connection $connection) {
        
    }
    
    /**
     * @inheritdoc
     */
    public function onError(Connection $connection, Exception $e) {
        
    }
}
