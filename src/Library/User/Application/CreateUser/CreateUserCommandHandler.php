<?php

declare(strict_types=1);

namespace Library\User\Application\CreateUser;

use Library\User\Domain\UserId;
use Library\User\Domain\UserName;
use Shared\Domain\Bus\Command\CommandHandler;

final class CreateUserCommandHandler implements CommandHandler
{
    public function __construct(
        private CreateUser $createUser
    ) {}

    public function __invoke(CreateUserCommand $command): void
    {
        $this->createUser->__invoke(
            new UserId($command->id()),
            new UserName($command->name())
        );
    }
}
