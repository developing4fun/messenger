<?php

declare(strict_types=1);

namespace Library\User\Domain\Exception;

use Library\User\Domain\UserId;
use Shared\Domain\Exception\DomainError;

final class UserAlreadyExists extends DomainError
{
    public function __construct(
        private UserId $userId
    ) {
        parent::__construct();
    }

    public function errorCode(): string
    {
        return 'user_already_exists';
    }

    public function errorMessage(): string
    {
        return sprintf(
            'User %s already exists and cannot be created',
            $this->userId->value()
        );
    }
}
