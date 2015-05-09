---
layout: default
---

# Installation
<!-- [[TOC]] -->

## Install Composer

To install TwoStream, you will first need to install [Composer][composer] if you haven't already.

## Install TwoStream

### Install via Composer

Before you start, you will have to add the repository below to your composer.json, because TwoStream currently depends on a slightly modified version of Ratchet, which I hope will be added in the 4.x release.

```php
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/CupOfTea696/Ratchet"
        }
    ],
```

You can install TwoStream by simply requiring the package with [Composer][composer] inside your projects root. To do so, run the following commands:

```bash
$ composer require cupoftea/twostream ~0.1.4-beta
$ composer update
```

### Setting up TwoStream

You will need to add the following service providers to your `config/app.php`:

```php
	'providers' => [
        
		/*
		 * Laravel Framework Service Providers...
		 */
        
        'Illuminate\Foundation\Providers\ArtisanServiceProvider',
        'Illuminate\Auth\AuthServiceProvider',
        'Illuminate\Bus\BusServiceProvider',
        
        ...
        
        'CupOfTea\TwoStream\TwoStreamServiceProvider',
        
	],
```
Though this step is optional, I do recommend adding the following Facade's in you `config/app.php` as well, because it makes TwoStream a lot easier to use and I will not supply documentation for use without the Facades. You can leave out the WsSession Facade if you do not need to access Session variables within your WebSocket Server.

```php
    'aliases' => [
        
		'App'       => 'Illuminate\Support\Facades\App',
		'Artisan'   => 'Illuminate\Support\Facades\Artisan',
		'Auth'      => 'Illuminate\Support\Facades\Auth',
		
		...
		
        'TwoStream' => 'CupOfTea\TwoStream\Facades\TwoStream',
        'WsRoute'   => 'CupOfTea\TwoStream\Facades\WsRoute',
        'WsSession' => 'CupOfTea\TwoStream\Facades\WsSession',
        
	],
```

Lastly, you will need to run the `twostream:install` command to publish and configure the required files.

```bash
$ php artisan twostream:install
```


[composer]: https://getcomposer.org/doc/00-intro.md

