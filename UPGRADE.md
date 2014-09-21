Upgrade from 2.6 to 2.7
=======================

Domain Events
-------------

Doctrine listener does not dispatch domain events through doctrine event manager
anymore.  It now does it through a Symfony event dispatcher.  The one configured
by default is the `event_dispatcher` service (Yes, it means that you have access
to the whole event dispatcher symfony integration, like the debug toolbar).

You must change your domain event listeners DIC definition:

Before:
```yaml
services:
    app.sync_listener:
        class: App\SyncListener
        tags:
            - { name: doctrine.event_listener, event: onUserActivated }
```

After:
```yaml
services:
    app.sync_listener:
        class: App\SyncListener
        tags:
            - { name: kernel.event_listener, event: UserActivated }
```

Delayed event listeners definition must also be updated:

Before:
```yaml
parameters:
    knp_rad.domain_event.delayed_event_names: [UserActivated]

services:
    app.async_listener:
        class: App\AsyncListener
        tags:
            - { name: doctrine.event_listener, event: onDelayedUserActivated }
```

After:
```yaml
parameters:
    knp_rad.domain_event.delayed_event_names: [UserActivated]

services:
    app.async_listener:
        class: App\AsyncListener
        tags:
            - { name: kernel.event_listener, event: UserActivated, method: onDelayedUserActivated }
```
