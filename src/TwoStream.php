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

use App;
use ZMQ;
use Route;
use Request;
use ZMQContext;

use CupOfTea\Package\Package;
use CupOfTea\TwoStream\Exception\TwoStreamException;
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
    const VERSION = '0.1.1-beta';
    
    /**
     * Socket Pull ID
     *
     * @const string
     */
    const SOCKET_PULL_ID = 'twostream.pull';
    
    /**
     * {@inheritdoc}
     */
    public function push($topic, $data, $recipient = 'all')
    {
        if (!config('twostream.push.enabled'))
            throw new TwoStreamException('Push is disabled, please enable it in the twostream configuration before using');
        
        $this->getSocket()->send(
            json_encode([
                'topic' => $topic,
                'data' => $data,
                'recipient' => $recipient,
            ])
        );
    }
    
    /**
     * Get ZMQSocket to push messages
     *
     * @return \ZMQSocket
     */
    protected function getSocket()
    {
        if (isset($this->socket)) {
            return $this->socket;
        } else {
            return $this->connectZmq();
        }
    }
    
    /**
     * Connect to socket
     *
     * @return \ZMQSocket
     */
    protected function connectZmq()
    {
        $context = new ZMQContext();
        $this->socket = $context->getSocket(ZMQ::SOCKET_PUSH, self::SOCKET_PULL_ID . '.' . App::environment());
        $this->socket->connect('tcp://localhost:' . config('twostream.push.port'));
        return $this->socket;
    }
    
}
