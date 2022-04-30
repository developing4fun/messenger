<?php

declare(strict_types=1);

namespace Library\User\Domain;

use Library\User\Domain\Event\UserCreated;
use Shared\Domain\Aggregate\AggregateRoot;

final class User extends AggregateRoot
{
    public function __construct(
        private UserId $userId,
        private UserName $userName
    ) {}

    public static function create(
        UserId $userId,
        UserName $userName
    ) :self {
        $user = new self($userId, $userName);
        $user->record(UserCreated::create($user));

        return $user;
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function userName(): UserName
    {
        return $this->userName;
    }
}
