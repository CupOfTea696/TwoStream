[![Latest Stable Version](https://poser.pugx.org/cupoftea/twostream/version.svg)](https://packagist.org/packages/cupoftea/twostream) [![Total Downloads](https://poser.pugx.org/cupoftea/twostream/downloads.svg)](https://packagist.org/packages/cupoftea/twostream) [![Latest Unstable Version](https://poser.pugx.org/cupoftea/twostream/v/unstable.svg)](https://packagist.org/packages/cupoftea/twostream) [![License](https://poser.pugx.org/cupoftea/twostream/license.svg)](https://packagist.org/packages/cupoftea/twostream)

# TwoStream
### Two-way communication between Laravel and your Client

TwoStream is a WebSocket server for Laravel 5, built upon [Ratchet](http://socketo.me). This package is currently in **alpha** stage and not complete. Certain parts work but are not properly tested.
Documentation coming in first beta. For now, the info below will have to do.
TwoStream is Laravel 5 only.

## Completed:
 - ServiceProvider and Facades, add `'CupOfTea\TwoStream\TwoStreamServiceProvider',` to your providers, and `'TwoStream' => 'CupOfTea\TwoStream\Facades\TwoStream',` and `'WsRoute'   => 'CupOfTea\TwoStream\Facades\WsRoute',` to your aliases in `config/app.php`
 - Installation command; run `twostream:install` before trying to use anything. (seriously!)
 - WebSocket Server, boot with `twostream:listen`.
 - Routing, use `WsRoute::call`, `WsRoute::publish`, `WsRoute::subscribe`, `WsRoute::unsubscribe` or `WsRoute::controller` to define WebSocket routes in `app/Ws/routes.php`. For implicit controllers, use the verbs `call`, `publish`, `subscribe` or `unsubscribe` and define your functions according to the [Laravel Docs](http://laravel.com/docs/5.0/controllers#implicit-controllers).
 - Read-only Session data available in Ws Controllers via the WsSession Facade, add `'WsSession' => 'CupOfTea\TwoStream\Facades\WsSession',` to your aliases in `config/app.php`
 - Send response to all subscribers, all excluding requestee or requestee.
 
## TODO:
 - Push events from server to all or specific user. (to sessionId = &#10006;, to user &#10006;) (zmq)
 - Middleware for connections. (or something similar) [@beta]
 - Online Documentation [@beta]
 
### Acknowledgements
TwoStream is heavily based on [Latchet][latchet]. Some of the internal workings and public API is entirely different, other parts are near copy-pasted from the original code. The main difference between TwoStream and [Latchet][latchet] is (or will be) Laravel 5 support and read-only access to Session data. If you are using Laravel 4, please do go and use [Latchet][latchet] since it is pretty good. (Although the creator claims it's not even an alhpa version. Believe me, it is.) If you use Laravel 5 however, feel free to start testing this out and contribute.

[latchet]: https://github.com/sidneywidmer/Latchet  "Latchet (L4 Package)"
