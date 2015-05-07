# WsSession
<!-- [[TOC]] -->

## Retrieving Session data

You can retrieve Session data in your WebSocket Requests from the Session in the same way as you're used to from Laravel, only you have to use the WsSession Facade instead of Laravel's Session Facade. Using Laravel's own Session Facade will not work. The WsSession Facade is read-only, this is done intentionally. Please do not try to write data to the Session in any way. This will only lead to unexpected results.

## Examples

#### Retrieving An Item From The Session

```php
WsSession::get('key');
```

#### Retrieving All Data From The Session

```php
$data = WsSession::all();
```

## Available Methods

 - `getId()`
 - `isValidId($id)`
 - `getName()`
 - `has($name)`
 - `get($name, $default = null)`
 - `hasOldInput($key = null)`
 - `getOldInput($key = null, $default = null)`
 - `all()`
 - `isStarted()`
 - `token()`
 - `getToken()`
 - `previousUrl()`

_**Note**: All methods are, although available, not always relevant. `previousUrl` for example will only return HTTP Requests, and does not contain a WS Request._
