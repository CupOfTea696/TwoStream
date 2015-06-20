# Events
<!-- [[TOC]] -->

TwoStream fires a few commands at certain points. You can listen for those events inside your Application to respond to them.

## Server Started

This event gets fired when the TwoStream Server is started. The IP on which the Server is listening will be attached to this event.

```php
$listen = [
    'CupOfTea\TwoStream\Events\ServerStarted' => [],
];
```

## Server Stopped

This event gets fired when the TwoStream Server is stopped. Be aware that this command only gets fired when you use the `TwoStream::stop` Method or the `twostream:stop` Console Command.

```php
$listen = [
    'CupOfTea\TwoStream\Events\ServerStopped' => [],
];
```

## Client Connected

This Event gets fired when a Client connects to the TwoStream WebSocket Server. The Client Id, which is the same as the Client's Session Id, will be attached to this event.

```php
$listen = [
    'CupOfTea\TwoStream\Events\ClientConnected' => [],
];
```

## Client Disconnected

This Event gets fired when a Client disconnects from the TwoStream WebSocket Server. The Client Id will be attached to this event.

```php
$listen = [
    'CupOfTea\TwoStream\Events\ClientDisconnected' => [],
];
```
