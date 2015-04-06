<?php namespace CupOfTea\TwoStream\Contracts;

use Exception;
use Ratchet\ConnectionInterface as Connection;

interface Dispatcher{
    
    public function onPublish(Connection $conn, $topic, $event, array $exclude, array $eligible);
    
    public function onCall(Connection $conn, $id, $topic, array $params);
    
    public function onSubscribe(Connection $conn, $topic)
    
    public function onUnSubscribe(Connection $conn, $topic)
    
    public function onOpen(Connection $conn);
    
    public function onClose(Connection $conn);
    
    public function onError(Connection $conn, Exception $e);
}