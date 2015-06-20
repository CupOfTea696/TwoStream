---
layout: default
---

<!-- header start -->
[![Latest Stable Version](https://poser.pugx.org/cupoftea/twostream/version.svg)](https://packagist.org/packages/cupoftea/twostream) [![Total Downloads](https://poser.pugx.org/cupoftea/twostream/d/total.svg)](https://packagist.org/packages/cupoftea/twostream) [![Latest Unstable Version](https://poser.pugx.org/cupoftea/twostream/v/unstable.svg)](https://packagist.org/packages/cupoftea/twostream) [![License](https://poser.pugx.org/cupoftea/twostream/license.svg)](https://packagist.org/packages/cupoftea/twostream)

# TwoStream <sup>{{ Beta }}</sup>
### Two-way communication between Laravel and the Client
<!-- header end -->

TwoStream is a WebSocket server for [Laravel 5][l5], built upon [Ratchet](http://socketo.me). This package is currently in **beta** stage and not complete. The most important functionality has been built but could use some more testing.

TwoStream is [Laravel 5][l5] only.

 - [Documentation](http://twostream.cupoftea.io/docs/)
 - [API Explorer](http://twostream.cupoftea.io/docs/api/)


## Notice:
TwoStream currently depends on a slightly modified version of Ratchet, which I hope will be added in the 4.x release. However, for the time being to get this dependency you need to add the repository and requirement below in your `composer.json`.

For more information on how to install TwoStream, please visit the [Documentation](http://twostream.cupoftea.io/docs/installation/)

```php
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/CupOfTea696/Ratchet"
        }
    ],
	"require": {
        "cboden/ratchet": "dev-master",
	},
```

## Completed:
 - ServiceProvider and Facades, add `'CupOfTea\TwoStream\TwoStreamServiceProvider',` to your providers, and `'TwoStream' => 'CupOfTea\TwoStream\Facades\TwoStream',` and `'WsRoute'   => 'CupOfTea\TwoStream\Facades\WsRoute',` to your aliases in `config/app.php`
 - Installation command; run `twostream:install` before trying to use anything. (seriously!)
 - WebSocket Server, boot with `twostream:listen`.
 - Routing, use `WsRoute::call`, `WsRoute::publish`, `WsRoute::subscribe`, `WsRoute::unsubscribe` or `WsRoute::controller` to define WebSocket routes in `app/Ws/routes.php`. For implicit controllers, use the verbs `call`, `publish`, `subscribe` or `unsubscribe` and define your functions according to the [Laravel Docs](http://laravel.com/docs/5.0/controllers#implicit-controllers).
 - Read-only Session data available in Ws Controllers via the WsSession Facade, add `'WsSession' => 'CupOfTea\TwoStream\Facades\WsSession',` to your aliases in `config/app.php`
 - Send response to all subscribers, all excluding requestee or requestee.
 - Push events from server to all or specific user (using sessionId)
 
## TODO:
 - Push events from server to all or specific user. (to sessionId = &#10003;, to username/user object &#10006;) (zmq)
 - Clean up some code
 - Improve Error Handling
 
### Acknowledgements
TwoStream is heavily based on [Latchet][latchet]. Some of the internal workings and public API is entirely different, other parts are near copy-pasted from the original code. The main difference between TwoStream and [Latchet][latchet] is [Laravel 5][l5] support and read-only access to Session data. If you are using Laravel 4, please do go and use [Latchet][latchet] since it is pretty good. (Although the creator claims it's not even an alpha version. Believe me, it is.) If you use [Laravel 5][l5] however, feel free to start testing this out and contribute.

[l5]: https://github.com/laravel/framework/ "Laravel 5"
[latchet]: https://github.com/sidneywidmer/Latchet  "Latchet (L4 Package)"
