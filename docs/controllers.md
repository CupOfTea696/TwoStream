# Controllers
<!-- [[TOC]] -->

## Introduction

Controllers for WebSocket Routes are located in `app/Ws/Controllers`. It is highly recommended that you use Controllers instead of Closures in your routes file.

## Basic Controllers

Here is an example of a basic Controller Class:

```php
namespace App\Ws\Controllers;

use App\Ws\Controllers\Controller;

class ChatController extends Controller
{
    
    /**
     * Connect the user to given chatroom
     *
     * @param  int  $id
     * @return Response
     */
    public function roomConnect($id)
    {
        return 'You connected to chat room ' . $id;
    }
    
}
```

Routing to the Controller Action works in the same way you're used to from Laravel:

```php
WsRoute::subscribe('chat/room/{id}', 'ChatController@roomConnect');
```

_**Note:** All controllers should extend the base `Ws\Controller` class, not Laravel's base Controller class!_

## Special Controllers

Laravel provides two special types of Controllers: Implicit and RESTful Resource Controllers. Implicit Controllers work in the same way they do in Laravel, so if you're not sure on how they work go check the [Documentation](http://laravel.com/docs/5.0/controllers#implicit-controllers).

RESTful Controllers do not exist within TwoStream. The `WsRoute` Facade does not provide the `resource()` function, and attempting to use it will result in an error.
