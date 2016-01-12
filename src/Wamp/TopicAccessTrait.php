<?php namespace CupOfTea\TwoStream\Wamp;

trait TopicAccessTrait
{
    
    /**
     * @var array
     */
    protected $topics = [];
    
    /**
     * Make all Topics available in the WampServerInterface
     *
     * @param array
     * @return void
     */
    public final function setTopics(&$topics)
    {
		$this->topics = &$topics;
	}
    
    public final function getTopics()
    {
        return $this->topics;
    }
}