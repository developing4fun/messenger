<?php

declare(strict_types=1);

namespace Library\User\Application\IncrementCounter;

use Library\User\Domain\Event\UserCreated;
use Shared\Domain\Bus\Event\DomainEventSubscriber;
use function dump;

final class IncrementCounterOnUserCreated implements DomainEventSubscriber
{
    public static function subscribedTo(): array
    {
        return [
            UserCreated::class,
        ];
    }

    public function __invoke(UserCreated $event): void
    {
        dump("INCREMENT");
    }
}
