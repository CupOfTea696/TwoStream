<?php namespace CupOfTea\TwoStream;

/**                                                                                      **
 *                                 `.-/++ossssssssoo+/:-.`                                *
 *                            `-/osyyhhhhhhhhhhhhhhhhhhyyyo+-.                            *
 *                         ./oyyhhhhhhhhhhhhhhhhhhhhhhhhhhhhhys/-`                        *
 *                      .:syhhhhhhhhhhhhhhhyssssyhhhhhhhhhhhhhhhys/.                      *
 *                    .+yhhhhhhhhhhhhhhhhy:.````.:yhhhhhhhhhhhhhhhhyo-`                   *
 *                  .oyhhhhhhhhhhhhhhhhhy. `osso` .yhhhhhhhhhhhhhhhhhhs-                  *
 *                `+yhhhhhhhhhhhhhhhhhhhs` -yhhy. `shhhhhhhhhhhhhhhhhhhho.                *
 *               -shhhhhhhhhhhhhhhhhhhhhh+` .--. `+hhhhhhhhhhhhhhhhhhhhhhy:               *
 *              :yhhhhhhhhhhhhhhhhhhhhhhhhyo    oyhhhhhhhhhhhhhhhhhhhhhhhhh+`             *
 *             /hhhhhhhhhhhhhhhhhhhhhhhhhhhh    hhhhhhhhhhhhhhhhhhhhhhhhhhhho`            *
 *            /hhhhhhhhhhhho+++++++++++++++/    /++++++++++++++++yhhhhhhhhhhho            *
 *           -hhhhhhhhhhhho`                                     /hhhhhhhhhhhh/           *
 *          `shhhhhhhhhhhh+////////////////-    -/////////////////hhhhhhhhhhhhy-          *
 *          /hhhhhhhhhhhhhhhhhhhhhhhhhhhhhh+    +hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhs          *
 *          yhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh+    +hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh.         *
 *         .hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh/    /hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh/         *
 *         -hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh:    :hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhho         *
 *         :hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh-    -hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhho         *
 *         :hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh-    -hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhho         *
 *         .hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh.    .hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh/         *
 *          yhhhhhhhhhhhhhhhhhhhhhhhhhhhhhy`    .yhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh-         *
 *          +hhhhhhhhyshhhhhhhhhhhhhhhhhhhy`    `yhhhhhhhhhhhhhhhhhhhhohhhhhhhhs          *
 *          `yhhhhhhho`+hhhhhhhhhhhhhhhhhhy`    `yhhhhhhhhhhhhhhhhhhy-.hhhhhhhh:          *
 *           :hhhhhhhy  :shhhhhhhhhhhhhhhhs      shhhhhhhhhhhhhhhhy+` :hhhhhhh+           *
 *            +hhhhhhh:  `/shhhhhhhhhhhhhhs      shhhhhhhhhhhhhhy+.  `shhhhhhs`           *
 *            `+hhhhhhy:   `-+syhhhhhhhhhho      ohhhhhhhhhhhyo:`   `ohhhhhhs.            *
 *              +hhhhhhy+`    `.:/osyyhhhho      ohhhhyyso+:.`     -shhhhhho`             *
 *               :yhhhhhhy/.       ``..--:.      -:---.``       `-oyhhhhhy+`              *
 *                .ohhhhhhhy+-`                              `./syhhhhhhs-                *
 *                  -shhhhhhhhyo/-``                     `.-+syhhhhhhhy/`                 *
 *                   `:oyhhhhhhhhhyso+:--...`````...-:/+osyhhhhhhhhhs/`                   *
 *                      -+yhhhhhhhhhhhhhhyyyyyyyyyyhhhhhhhhhhhhhhyo:`                     *
 *                        `-+syhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhyyo:.                        *
 *                           `.:osyyhhhhhhhhhhhhhhhhhhhhhyso/-`                           *
 *                                `-:/+ossyyyyyyyyysso/:-.`                               *
 *                                        ```..````                                       *
 **                                                                                      **/

use ZMQ;
use Route;
use Request;
use ZMQContext;

use CupOfTea\Package\Package;
use CupOfTea\TwoStream\Contracts\Provider as ProviderContract;

class TwoStream implements ProviderContract
{
    
    use Package;
    
    /**
     * Package Info
     * 
     * @const string
     */
    const PACKAGE = 'CupOfTea/TwoStream';
    const VERSION = '0.0.14-alpha';
    
    /**
     * This package's configuration
     *
     * @var array
     */
    protected $cfg;
    
    /**
     * Create a new provider instance.
     *
     * @param  string  $cfg
     * @return void
     */
    public function __construct($cfg)
    {
        $this->cfg = $cfg;
    }
    
    /**
     * {@inheritdoc}
     */
    public function push($channel, $message)
    {
        if (!$this->enablePush)
            throw new TwoStreamException('Push is disabled');
        
        $message = array_merge(array('topic' => $channel), $message);
        $this->getSocket()->send(json_encode($message));
    }
    
    /**
     * get zmqSocket to push messages
     *
     * @return ZMQSocket instance
     */
    protected function getSocket()
    {
        if(isset($this->socket)) {
            return $this->socket;
        } else {
            return $this->connectZmq();
        }
    }
    /**
     * Connect to socket
     *
     * @return ZMQSocket instance
     */
    protected function connectZmq()
    {
        $context = new ZMQContext();
        $this->socket = $context->getSocket(ZMQ::SOCKET_PUSH, Config::get('latchet::socketPushId', sprintf('latchet.push.%s', App::environment())));
        $this->socket->connect("tcp://localhost:".Config::get('latchet::zmqPort'));
        return $this->socket;
    }
    
}
