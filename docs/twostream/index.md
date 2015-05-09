---
layout: default
---

# TwoStream
<!-- [[TOC]] -->

## Push

TwoStream allows you to push messages from your server to one or more Clients. You could do this on certain events in a cronjob, at the end of a queued task, or anywhere else you see fit. Pushing messages to a Client happens via the TwoStream Facade. Please note that in order to receive that data, the Client needs to be subscribed to the topic (or route) you push to. The recipients can be specified by either a SessionID or an array of Session ID's.

```php
// Push data to all Clients
TwoStream::push('queued/task/complete', $data);

// Push data to specific Client(s)
TwoStream::push('queued/task/complete', $data, [$redClient->sessionId, $blueClient->sessionId]);
```
