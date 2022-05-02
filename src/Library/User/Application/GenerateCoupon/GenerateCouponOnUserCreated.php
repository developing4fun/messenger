<?php

declare(strict_types=1);

namespace Library\User\Application\GenerateCoupon;

use Library\User\Domain\Event\UserCreated;
use Shared\Domain\Bus\Event\DomainEventSubscriber;

final class GenerateCouponOnUserCreated implements DomainEventSubscriber
{
    public static function subscribedTo(): array
    {
        return [
            UserCreated::class,
        ];
    }

    public function __invoke(UserCreated $event): void
    {
        dump("GENERATE COUPON");
    }
}
