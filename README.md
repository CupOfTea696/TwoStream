[![Latest Stable Version](https://poser.pugx.org/leaphly/cart-bundle/version.svg)](https://packagist.org/packages/cupoftea/twostream) [![Total Downloads](https://poser.pugx.org/cupoftea/twostream/downloads.svg)](https://packagist.org/packages/cupoftea/twostream) [![Latest Unstable Version](https://poser.pugx.org/cupoftea/twostream/v/unstable.svg)](https://packagist.org/packages/cupoftea/twostream) [![License](https://poser.pugx.org/cupoftea/twostream/license.svg)](https://packagist.org/packages/cupoftea/twostream)

# TwoStream
### Two-way WebSocket communication for Laravel 5

This package is currently in **alpha** stage and not complete. Certain parts work but are not properly tested.
Documentation coming in first beta. For now, the info below will have to do.
TwoStream is Laravel 5 only.

## Completed:
 - ServiceProvider and Facades, add `'CupOfTea\TwoStream\TwoStreamServiceProvider',` to your providers, and `'TwoStream' => 'CupOfTea\TwoStream\Facades\TwoStream',` and `'WsRoute'   => 'CupOfTea\TwoStream\Facades\WsRoute',` to your aliases in `config/app.php`
 - Installation command; run `twostream:install` before trying to use anything. (seriously!)
 - WebSocket Server, boot with `twostream:listen`.
 - Routing, use `WsRoute::call`, `WsRoute::publish`, `WsRoute::subscribe` or `WsRoute::unsubscribe` to define WebSocket routes in `app/Ws/routes.php`. (partially complete)
 
## TODO:
 - Map WsRoute::controller to correct functions, allow missing functions.
 - Handle calls to unmapped routes properly. (currently responds with HTTP 404 HTML I believe)
 - Middleware for connections.
 - Make more data available in Controllers. (e.g. read-only Session data)
 - Push events from server to all or specific user.
 
### Acknowledgements
TwoStream is heavily based on [Latchet][latchet]. Some of the internal workings and public API is entirely different, other parts are near copy-pasted from the original code. The main difference between TwoStream and [Latchet][latchet] is (or will be) Laravel 5 support and read-only access to Session data. If you are using Laravel 4, please do go and use [Latchet][latchet] since it is pretty good. (Although the creator claims it's not even an alhpa version. Believe me, it is.) If you use Laravel 5 however, feel free to start testing this out and contribute.

[latchet]: https://github.com/sidneywidmer/Latchet  "Latchet (L4 Package)"
