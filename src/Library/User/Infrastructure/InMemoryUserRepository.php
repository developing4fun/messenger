<?php

declare(strict_types=1);

namespace Library\User\Infrastructure;

use Library\User\Domain\User;
use Library\User\Domain\UserId;
use Library\User\Domain\UserRepository;

final class InMemoryUserRepository implements UserRepository
{
    public function byId(UserId $userId): ?User
    {
        return null;
    }

    public function save(User $user): void
    {
    }
}
