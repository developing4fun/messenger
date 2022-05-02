<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Symfony\Bus\Event;

use Shared\Domain\Bus\Event\DomainEvent;
use Shared\Domain\Bus\Event\EventBus;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

final class SymfonyEventBus implements EventBus
{
    public function __construct(
        private MessageBusInterface $bus
    ) {}

    public function publish(DomainEvent ...$events): void
    {
        foreach ($events as $event) {
            try {
                $this->bus->dispatch(
                    (new Envelope($event))->with(
                        new DispatchAfterCurrentBusStamp(),
                        new AmqpStamp($event::eventName())
                    )
                );
            } catch (NoHandlerForMessageException) {
            }
        }
    }
}
