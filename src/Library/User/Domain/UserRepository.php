<?php

declare(strict_types=1);

namespace Library\User\Domain;

interface UserRepository
{
    public function byId(UserId $userId): ?User;
    public function save(User $user): void;
}
