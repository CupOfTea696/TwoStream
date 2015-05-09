---
layout: default
---

# Requests
<!-- [[TOC]] -->

## Publish Data

To access the data sent with a PUBLISH Request, you can simply do this by retrieving the input field `data` like so:

```php
$data = Request::input('data');
```

No other input data is available, so running `Request::all()` will return an array with a single key `data`. The PUBLISH Request is the only request that has data, so in all other Requests the `data` variable will be empty.

_**Note**: Although all [Flash](http://laravel.com/docs/5.0/requests#old-input) methods are available, they will have no effect. Using it would simply create a new session on the server, that never gets assigned to the Client. Please do not attempt Flashing data or retrieving Flash data._

## Cookies

Although [Cookie data](http://laravel.com/docs/5.0/requests#cookies) is accesible through the standard Laravel methods, it is highly recommended not to access it unless you absolutely need to and are sure that the Cookie data won't have changed since the WebSocket Connection has been opened. The Cookie data could become out of date if you Client has you Application opened in another window.

In addition to that, writing Cookie data will have no effect on the client, since all responses on WebSocket Requests are sent through the WebSocket Protocol and not the HTTP Protocol. The changed cookie is therefore never sent to the client.

**TL;DR**: Don't try and use Cookies unless you know what you are doing.
