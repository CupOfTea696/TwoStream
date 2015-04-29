---
layout: default
---

---
layout: default
---

# Routing

TwoStream provides an easy way to handle WebSocket Topic Events through the provided WsRoute Facade. This allows you to blend in your code in a way that should already be very familliar to you from the default Laravel Routes and Controllers. To put it simple: Anything you can do with an HTTP request, you can now do for Topic Events, in virtually the same way!

## Basic Routing

You can define your Topic Event Routes for your application in the `app/Ws/routes.php` file, which is your TwoStreams's equivalent to the `app/Http/routes.php` file you know from Laravel. In addition to that, the route's Topic (equivalent to URL in your HTTP routes) supports both values in the format `chat/room/{id}` and `com.myapp.multiply`.

### SUBSCRIBE Routes

The Subscribe Event gets triggered when a Client subscribes to a topic. In the example below, we have a callback function that gets triggered every time a Client subscribes to a chat room.

```
WsRoute::subscribe('chat/room/{id}', function($id)
{
    return 'You connected to chat room ' . $id;
});
```

### UNSUBSCRIBE Routes

The Unsubcribe Event is the opposite of the subscribe event, and gets triggered every time a Client unsubscribes from a topic.

```
WsRoute::unsubscribe('chat/room/{id}', function($id)
{
    return 'You disconnected to chat room ' . $id;
});
```

### PUBLISH Routes

The Publish Event is triggered when a client Publishes data to a topic. The data can be retrieved by using `Request::input('data');`. More info on Publish Data can be found in the Controllers Documentation.

```
WsRoute::publish('blog/create', function()
{
    $data = Request::input('data');
});
```

### CALL Routes

The Call Event is triggered when the Client performs an RPC to the WebSocket Server. This is the only route that requires a response. If the Procedure does not need to return any data to the Client, you can just return true and the default success message will be sent. If you don't return anything however, an error message is sent to the Client.

```
WsRoute::call('math/mult/{a}/{b}', function($a, $b)
{
    return $a * $b;
});
```

## Notes

Please know that although all above examples use Closures, the recommended way to handle Events is through Controllers, which will be addressed in the next chapter.