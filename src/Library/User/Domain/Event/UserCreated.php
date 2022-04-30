<?php

declare(strict_types=1);

namespace Library\User\Domain\Event;

use Library\User\Domain\User;
use Shared\Domain\Bus\Event\DomainEvent;

final class UserCreated extends DomainEvent
{
    public function __construct(
        string $userId,
        private string $userName,
        ?string $eventId = null,
        ?string $occurredOn = null
    ) {
        parent::__construct($userId, $eventId, $occurredOn);
    }

    public static function create(User $user): self
    {
        return new self($user->userId()->value(), $user->userName()->value());
    }

    static public function eventName(): string
    {
        return 'user.created';
    }

    static public function fromPrimitives(
        string $aggregateId,
        array $body,
        string $eventId,
        string $occurredOn
    ): DomainEvent {
        return new self(
            $aggregateId,
            $body['userName'],
            $eventId,
            $occurredOn
        );
    }

    public function toPrimitives(): array
    {
        return [
            'userName' => $this->userName,
        ];
    }

    public function userName(): string
    {
        return $this->userName;
    }
}
