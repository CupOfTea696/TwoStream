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
$ composer require cupoftea/twostream ~1.0
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

Lastly, you will need to run the `twostream:install` command to publish and configure the required files. You won't be able to access any TwoStream functionality before you run this command.

```bash
$ php artisan twostream:install
```

## Push Requirements

In order to use TwoStream's Push functionality, you must have the ZeroMQ PHP extension installed.

### Installing ZeroMQ

Go to the [ZMQ website](http://zeromq.org/intro:get-the-software), and copy the link to the latest stable release. Then on your server run the following commands:

```bash
$ wget http://download.zeromq.org/zeromq-4.1.1.tar.gz # Use the latest release!
$ tar -xvzf zeromq-4.1.1.tar.gz
$ cd zeromq-4.1.1
$ ./configure
$ make
$ sudo make install
$ sudo ldconfig # On Linux only
```

Next, you need to install the PHP Language binding.

```bash
pecl install zmq-beta
```

Next you need to add the extension to your php.ini. With PHP-FPM this means adding a symlink.

```bash
$ ln -s /etc/php5/mods-available/zmq.ini /etc/php5/fpm/conf.d/20-zmq.ini
$ ln -s /etc/php5/mods-available/zmq.ini /etc/php5/cli/conf.d/20-zmq.ini
```

Lastly, you need to add the react/zmq dependency to your project's composer.json file.

```bash
$ composer require react/zmq 0.3.*
```

[composer]: https://getcomposer.org/doc/00-intro.md

