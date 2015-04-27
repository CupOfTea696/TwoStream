---
layout: default
---


# Configuration

## Publishing the Configuration

To publish the TwoSTream configuration file in your Application, run the following command

```
$ php artisan vendor:publish --provider="cupoftea/twostream" --tag="cfg"
```

Once you have done that, you can find the TwoStream configuration inside `config/twostream.php`. This step is optional, and if you know which settings you want to change, you can just create the `config/twostream.php` file yourself and place those settings inside.

## Configuring TwoStream

You only need to overwrite the settings you want to change. You can remove any items where you want to use the default settings from your Application's TwoStream Configuration file. Below is a list of all avialable settings.

### Response Settings

These are the default settings for how TwoStream should handle responses:

 - `response.recipient`: To which clients the Controller Response should be sent to if no client is specified. By default this is set to `'requestee'` so you don't accidentally send sensitive data to all users. You can modify this setting as you like. This setting supports the following options:
   - `'all'`: The response will be sent to all subscribed Clients.
   - `'except'`: The response will be sent to all subscribed Clients except the Client that made the request.
   - `'requestee'`: The response will be sent to the Client that made the request.
 - `response.rpc.enabled`: Enable/Disable RPCs for your Application. This setting is disabled by default.
 - `response.rpc.success`: Default response message for a successful RPC.
 - `response.rpc.error.enabled`: Default error message for when the Procedure is not found.
 - `reponse.rpc.error.disabled`: Default error message for when RPC is disabled.

### WebSocket Settings

These are the settings for the TwoStream WebSocket Server:

 - `websocket.port`: Default port on which the React Socket Server will listen for incoming connections. This option can also be set in the artisan command. More info on how to do that in the [Server Documentation](). The default port is `1111`.

### Push Settings

These are the settings for pushing messages from Server to Client:

 - `push.enabled`: Enable/Disable push messages from your Server. This setting is disabled by default and **requires the ZeroMQ Library** to work. More info on this can be found in the [Push Documentation]().
 - `push.port`: The default port for ZeroMQ Connections. This is used to we can connect to all Socket connections and broadcast messages from the back-end.

### Legacy Settings

 - `flash.allowed`: Allow legacy browsers to connect with the [websocket polyfill](https://github.com/gimite/web-socket-js).
 - `flash.port`: The web-socket-js fallback requires a Flash Socket Policy file. This setting allows you to change the port on which the Flash Policy is located. Don't forget to tell the Client where the polici is located. For example in JS: `WebSocket.loadFlashPolicyFile("xmlsocket://myhost.com:843");` This will cause a connection delay of about 2-3 seconds.

