<?php

declare(strict_types=1);

namespace Library\User\Application\CreateExternalUser;

final class CreateExternalUserCommand
{
    public function __construct(
        private string $userId,
        private string $userName
    ) {}

    public function userId(): string
    {
        return $this->userId;
    }

    public function userName(): string
    {
        return $this->userName;
    }
}
