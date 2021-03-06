<?php namespace CupOfTea\TwoStream\Wamp;

use Ratchet\Wamp\ServerProtocol;
use Ratchet\Wamp\WampServerInterface;
use Ratchet\Wamp\WampServer as RatchetWampServer;

class WampServer extends RatchetWampServer
{
    public function __construct(WampServerInterface $app)
    {
        $this->wampProtocol = new ServerProtocol(new TopicManager($app));
    }
}
