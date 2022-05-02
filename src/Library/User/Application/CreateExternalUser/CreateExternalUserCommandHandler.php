<?php

declare(strict_types=1);

namespace Library\User\Application\CreateExternalUser;

use Library\User\Domain\User;
use Library\User\Domain\UserId;
use Library\User\Domain\UserName;
use Library\User\Domain\UserRepository;
use Shared\Domain\Bus\Command\CommandHandler;

final class CreateExternalUserCommandHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function __invoke(CreateExternalUserCommand $command): void
    {
        $user = new User(
            new UserId($command->userId()),
            new UserName($command->userName())
        );

        dump("LLEGA eL EXTERNO");

        $this->userRepository->save($user);
    }
}
