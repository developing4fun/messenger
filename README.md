# Installation
Run `make build` in order to install all application dependencies (you must have Docker installed).

For more commands, type `make help`


# Conclusions
## TLDR;
We think `Messenger` is useful for:
- sync/async Command bus
- sync Query bus
- sync Event bus

... and definitively NOT SUITABLE for async Event bus (at least not for Domain Events).

## Details

- `Messenger` can create the exchange and the queues every time it tries to send a message. This behaviour can be changed via the `auto_setup` flag.
- `Messenger` is easy to use and allows multiple integrations, but the `messenger.yaml` file structure can be nasty to mantain:
    - You have to create at least one transport per consumer (x2 if you want to define `failure transports` such as dead-letters)
    - You have to "map" each event to at least one transport
    - These two conditions can make your `messenger.yaml` very difficult to maintain.

### Troubleshooting:
```yaml
...  
  transports:
      generate-coupon:
        dsn: '%env(RABBITMQ_URL)%'
        options:
          auto_setup: true
          exchange:
            name: '%env(RABBITMQ_EXCHANGE)%'
            type: topic
          queues:
            generate_coupon_on_user_created:
              binding_keys: [user.created]
...
routing:
  Library\User\Domain\Event\UserCreated: generate-coupon
```
This works. Let's create a scenario where we have two subscribers listening two different queues which receive the same event: `UserCreated`.

So, we have to create a second transport and binding it to the new queue:
```yaml
...
  transports:
      generate-coupon:
        dsn: '%env(RABBITMQ_URL)%'
        options:
          auto_setup: true
          exchange:
            name: '%env(RABBITMQ_EXCHANGE)%'
            type: topic
          queues:
            generate_coupon_on_user_created:
              binding_keys: [user.created]

      # Our second transport
      increment-counter:
        dsn: '%env(RABBITMQ_URL)%'
        options:
          auto_setup: true
          exchange:
            name: '%env(RABBITMQ_EXCHANGE)%'
            type: topic
          queues:
            increment_counter_on_user_created:
              binding_keys: [user.created]
...
  routing:
    Library\User\Domain\Event\UserCreated: [generate-coupon, increment-counter] # We add the second transport to the event
```

We try this aand... it doesn't work as expected. `Messenger` sends TWICE the message to each queue, and we expected having only one message.

In order to solve this we could delete both transports and have only one attached to the 2 queues. Let's try:
```yaml
...
  transports:
    create-user:
      dsn: '%env(RABBITMQ_URL)%'
      options:
        auto_setup: true
        exchange:
          name: '%env(RABBITMQ_EXCHANGE)%'
          type: topic
        queues:
          generate_coupon_on_user_created: # queue 1
            binding_keys: [user.created]
          increment_counter_on_user_created: # queue 2
            binding_keys: [user.created].
...
routing:
  Library\User\Domain\Event\UserCreated: create-user
```
We try this and it works, but... when consuming the queues, `Messenger` executes the subscribers in the same thread. This is a not desirable scenario because:
  - We have one transport (one consumer) doing one or more actions (because the transport is sending the same message to one or more consumers).
  - If one of the subscribers fails, the message will be retried and reprocessed, and again it will be passed to ALL the subscribers (even to the ones they already successfully processed it).
  - It breaks the rule "one consumer - one queue"
  - It doesn't scale very well
