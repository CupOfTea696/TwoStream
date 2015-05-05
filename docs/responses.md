# WS
<!-- [[TOC]] -->

## Basic Responses

All WebSocket responses are automatically encoded to JSON by TwoStream, so there is no need to convert any objects or arrays to JSON before returning them in your Controller or Closure.

Although it is possible to return views as a response, it is recommended not to do this. If you do want to use WebSockets to navigate pages (e.g. to replace all AJAX requests), only sending the required data through the WebSocket and using a front-end templating engine is a better approach.

## Targeted Responses

TwoStream provides an easy way to send your responses to a list of specific Clients. You can either use one of the [Pre-defined Recipient Lists](http://twostream.cupoftea.io/docs/responses/#pre-defined-recipient-lists) or specify Clients by their Session ID. To specify the response recipients, simply return an associative array with the keys `recipient` and `data`, where where `recipient` is a Session ID or an array of Session IDs, and `data` is the response you want to send. The ability to send Responses to a Client by username will be added in the future.

```php
// Pre-defined recipient list
return [
    'data' => 'hello world',
    'recipient' => 'all',
];

// Specific recipient
return [
    'data' => 'hello world',
    'recipient' => $sessionID,
];
```

### Pre-defined Recipient Lists

The following is a list of the Pre-defined Recipient Lists that you can use for [Targeted Responses](http://twostream.cupoftea.io/docs/responses/#targeted-responses):

 - `'all'`: The response will be sent to all subscribed Clients.
 - `'except'`: The response will be sent to all subscribed Clients except the Client that made the request.
 - `'requestee'`: The response will be sent to the Client that made the request.

_You can set a default recipient in your [Configuration](http://twostream.cupoftea.io/docs/configuration/#response-settings)._
