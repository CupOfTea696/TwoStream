---
layout: default
---

<!-- header start -->
[![Latest Stable Version](https://poser.pugx.org/cupoftea/twostream/version.svg)](https://packagist.org/packages/cupoftea/twostream) [![Total Downloads](https://poser.pugx.org/cupoftea/twostream/d/total.svg)](https://packagist.org/packages/cupoftea/twostream) [![Latest Unstable Version](https://poser.pugx.org/cupoftea/twostream/v/unstable.svg)](https://packagist.org/packages/cupoftea/twostream) [![License](https://poser.pugx.org/cupoftea/twostream/license.svg)](https://packagist.org/packages/cupoftea/twostream)

# TwoStream
### The WebSocket server for Laravel
<!-- header end -->

TwoStream is a WebSocket server for [Laravel 5.1][l5], built upon [Ratchet](http://socketo.me). Take advantage of fast two-way communication between your Application and the User.

TwoStream is [Laravel 5.1][l5] only.

 - [Documentation](http://twostream.cupoftea.io/docs/)
 - [API Explorer](http://twostream.cupoftea.io/docs/api/)


## Notice:
TwoStream currently depends on a slightly modified version of Ratchet in order to target specific recipients for your responses. Until this is implemented into Ratchet, you will need to add the repository and requirement below in your `composer.json`.

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

### Acknowledgements
TwoStream is heavily based on [Latchet][latchet]. Some of the internal workings and public API is entirely different, other parts are near copy-pasted from the original code. The main difference between TwoStream and [Latchet][latchet] is [Laravel 5.1][l5] support and read-only access to Session data. If you are using Laravel 4, please do go and use [Latchet][latchet] since it is pretty good. (Although the creator claims it's not even an alpha version. Believe me, it is.) If you use [Laravel 5.1][l5] however, feel free to start testing this out and contribute.

[l5]: https://github.com/laravel/framework/ "Laravel 5.1"
[latchet]: https://github.com/sidneywidmer/Latchet  "Latchet (L4 Package)"
