---
layout: default
---

---
layout: default
---

---
layout: default
---

---
layout: default
---

# Server

Once you have installed and configured TwoStream, starting the WebSocket server is really easy. Just run the following artisan command:

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
