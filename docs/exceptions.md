# Exceptions
<!-- [[TOC]] -->

To try and keep the WebSocket server running as smoothly as possible, TwoStream catches all Exceptions, syntax errors and recoverable fatal errors. By default, any Exception or error is logged, and sent to the front-end through the WebSocket connection.

## The Error Handler

Once you have installed TwoStream, an Error Handler has been published to deal with any Exceptions. You can find it in `app/Exceptons/WsHandler.php`. It contains two methods, `report` and `render`. They are inherently the same methods as the [Exception Handler](http://laravel.com/docs/5.1/errors#the-exception-handler) provided to you by Laravel.

### The Report Method

The `report` method is used to log Exceptions or send them to an external service. You can access the LoggerInterface using `$this->log`. You can also ignore Exceptions by type useing the `$dontReport` property, as explained in the [Laravel Documentation](http://laravel.com/docs/5.1/errors#the-exception-handler).

```php
public function report(Exception $e)
{
    if ($e instanceof SomeExceptiob) {
        //
    }
    
    return parent::report($e);
}
```

### The Render Method

The render method is very much alike, apart from the fact that you can return an Array or Object that will be sent as JSON through the WebSocket Connection. There is no need to specify a recipient in this case, for the response will always be sent to the [requestee](http://twostream.cupoftea.io/docs/responses/#pre-defined-recipient-lists).

Additionally, you can also sent output back to the Console, which the TwoStream Error Handler already does by default. To send output to the console, use the `line`, `info`, `comment`, `question` and `error` methods available on the Error Handler. Each of these methods will use the appropriate ANSI colors for their purpose.

```php
public function render(Exception $e)
{
    $this->error("Error: {$e->getMessage()}");
    
    return [
        'error' => [
            'msg' => $e->getMessage(),
            'domain' => 'php.' . str_replace('\_', '.', snake_case(get_class($e)))
        ]
    ];
}
```

_**Note:** It is highly recommended that you don't call the parent render method, for it sends the entire error include its stack trace back to the client._
