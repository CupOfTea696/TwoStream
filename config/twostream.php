<?php

/**
 *--------------------------------------------------------------------------
 * TwoStream Settings
 *--------------------------------------------------------------------------
 *
 * All settings indicated by @optional can be removed from this
 * file if you don't want to override the default settings.
 *
 */

return [
    /**
     *--------------------------------------------------------------------------
     * Response Settings
     *--------------------------------------------------------------------------
     *
     * Default settings for how TwoStream should handle responses.
     *
     */
    'response' => [
        /**
         *--------------------------------------------------------------------------
         * Recipient
         *--------------------------------------------------------------------------
         *
         * To which clients should the Controller response be send to if no client
         * is specified. Make sure you don't accidentally send sensitive
         * data to all clients when you set this option to
         * something other than 'requestee'!
         *
         * @optional
         * @default 'requestee'
         * @supported
         *      - 'all': all clients
         *      - 'except': all clients except the requestee
         *      - 'requestee': the client that made the request.
         *
         */
        'recipient' => 'requestee',
        
        /**
         *--------------------------------------------------------------------------
         * RCP Response Settings
         *--------------------------------------------------------------------------
         *
         * Default responses for Remote Procedure Calls (RPCs)
         *
         */
        'rpc' => [
            /**
             *--------------------------------------------------------------------------
             * Enable
             *--------------------------------------------------------------------------
             *
             * Enable Remote Procedure Calls
             *
             * @optional
             * @default false
             *
             */
            'enabled' => false,
            
            /**
             *--------------------------------------------------------------------------
             * Success
             *--------------------------------------------------------------------------
             *
             * Default success message.
             *
             * @optional
             * @default 'Success.'
             *
             */
            'success' => 'Success.',
            
            /**
             *--------------------------------------------------------------------------
             * Error
             *--------------------------------------------------------------------------
             *
             * Default error message. You can specify a message both for when RPC is
             * disabled and when the Procedure is not found.
             *
             * @optional
             *
             */
            'error' => [
                // @default 'This procedure does not exist.'
                'enabled' => 'This procedure does not exist.',
                
                // @default 'RPC not supported.'
                'disabled' => 'RPC not supported.',
            ],
        ],
    ],
    
    
    /**
     *--------------------------------------------------------------------------
     * WebSocket Settings
     *--------------------------------------------------------------------------
     *
     * Settings for the WebSocket Server
     *
     */
    'websocket' => [
        /**
         *--------------------------------------------------------------------------
         * Socket Default Port
         *--------------------------------------------------------------------------
         *
         * Default port on which the React Socket Server will listen for incoming
         * connections. You can also define a port in the artisan command,
         * if nothing is set there, we'll use this port.
         *
         * @optional
         * @default 1111
         *
         */
        'port' => 1111,
    ],
    
    /**
     *--------------------------------------------------------------------------
     * Push Settings
     *--------------------------------------------------------------------------
     *
     * Settings for pushing messages from Server to Client
     *
     */
    'push' => [
        /**
         *--------------------------------------------------------------------------
         * Enable Push Option
         *--------------------------------------------------------------------------
         *
         * TwoStream gives you the possibility to easily push messages to subscribed
         * Topics. To be able to push messages, you need to enable the
         * ZeroMQ Library (libzmq). It can be a little tricky
         * to install the library and the PECL extension.
         * A lot of hosters won't even allow you to
         * install something so it's optional
         * and you can enable it here.
         *
         */
        'enabled' => false,
        
        /**
         *--------------------------------------------------------------------------
         * ZeroMQ Socket Default Port
         *--------------------------------------------------------------------------
         *
         * Port for the ZeroMQ connection. This is used so we can connect to
         * all Socket connections and broadcast messages
         * from e.g an Ajax Request.
         *
         * @optional
         * @default 5555
         *
         */
        'port' => 5555,
    ],
    
    /**
     *--------------------------------------------------------------------------
     * Legacy Settings
     *--------------------------------------------------------------------------
     *
     * Settings for legacy browser support.
     *
     */
    'flash' => [
        /**
         *--------------------------------------------------------------------------
         * Allow Flash
         *--------------------------------------------------------------------------
         *
         * Allow legacy browsers to connect with the websocket polyfill
         * https://github.com/gimite/web-socket-js
         *
         * @optional
         * @default true
         *
         */
        'allowed' => true,
        
        /**
         *--------------------------------------------------------------------------
         * Flash Port
         *--------------------------------------------------------------------------
         *
         * If Flash is allowed and Websockets are not supported by the client
         * browser, you have to provide a Flash socket policy file for the
         * web-socket-js fallback.
         *
         * This is automatically done by TwoStream. However, you have to set a port on which
         * this policy is located. You are free to set your own port here, if you are
         * not allowed to bind something to some of the lower ports.
         *
         * This will cause a connection delay of 2-3 seconds, and don't forget to
         * tell the client where the policy is located. In JS:
         * WebSocket.loadFlashPolicyFile("xmlsocket://myhost.com:843");
         *
         * @optional
         * @default 843
         *
         */
        'port' => 843,
    ],
    
];
