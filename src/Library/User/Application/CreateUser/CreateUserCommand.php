<?php

declare(strict_types=1);

namespace Library\User\Application\CreateUser;

use Shared\Domain\Bus\Command\Command;

final class CreateUserCommand implements Command
{
    public function __construct(
        private string $id,
        private string $name
    ) {}

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }
}
