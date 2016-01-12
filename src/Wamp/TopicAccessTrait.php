<?php namespace CupOfTea\TwoStream\Wamp;

trait TopicAccessTrait
{
    
    /**
     * @var array
     */
    protected $topics = [];
    
    /**
     * Make all Topics available in the WampServerInterface.
     *
     * @param array
     * @return void
     */
    final public function setTopics(&$topics)
    {
        $this->topics = &$topics;
    }
    
    final public function getTopics()
    {
        return $this->topics;
    }
}
