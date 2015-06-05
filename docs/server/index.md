---
layout: default
---

# Running TwoStream
<!-- [[TOC]] -->

## Server

Once you have installed and configured TwoStream, starting the WebSocket server is really easy. Just run the Artisan command below. To make sure the TwoStream server keeps running, you may use a process monitor such as [Supervisor][supervisor].

```bash
$ php artisan twostream:listen
```

## Options

The `twostream:listen` command allows you to set a bunch of options when you start the Server.

 - `-g or --port `: Equivalent of [`websocket.port`](docs/configuration/#websocket-settings) in the configuration.
 - `-p or --push `: Equivalent of [`push.enabled`](docs/configuration/#push-settings) in the configuration. Set to `true` or `false`.
 - `-P or --push-port`: Equivalent of [`push.port`](docs/configuration/#push-settings) in the configuration.
 - `-f or --flash `: Equivalent of [`flash.allowed`](docs/configuration/#legacy-settings) in the configuration. Set to `true` or `false`.
 - `-F or --flash-port`: Equivalent of [`push.port`](docs/configuration/#legacy-settings) in the configuration.

## Examples

```bash
# Run TwoStream on port 5555:
$ php artisan twostream:listen -g 5555

# Enable Push on port 2222:
$ php artisan twostream:listen -p true -P 2222
```

## Stopping the server

You can also stop the server by running the Artisan command below. If you are using [Supervisor][supervisor] and want the server to automatically restart even when you manually stop it, you will need to set the `autorestart` option to `true` instead of the default `unexpected`.

```bash
$ php artisan twostream:stop
```

[supervisor]: http://supervisord.org/
