<?php namespace CupOfTea\TwoStream\Wamp;

use Ratchet\Wamp\WampServerInterface;
use Ratchet\Wamp\TopicManager as RatchetTopicManager;

class TopicManager extends RatchetTopicManager
{
    /**
     * {@inheritdoc}
     */
    public function __construct(WampServerInterface $app)
    {
        $this->app = $app;
        
        if (method_exists($this->app, 'setTopics')) {
            $this->app->setTopics($this->topicLookup);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function onSubscribe(ConnectionInterface $conn, $topic)
    {
        $topicObj = $this->getTopic($topic);

        if ($conn->WAMP->subscriptions->contains($topicObj)) {
            return;
        }

        $topicObj->add($conn);
        $conn->WAMP->subscriptions->attach($topicObj);
        $this->app->onSubscribe($conn, $topicObj);
    }
}
