<?php

declare(strict_types=1);

namespace Library\User\Application\CreateUser;

use Library\User\Domain\Exception\UserAlreadyExists;
use Library\User\Domain\User;
use Library\User\Domain\UserId;
use Library\User\Domain\UserName;
use Library\User\Domain\UserRepository;

final class CreateUser
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function __invoke(
        UserId $userId,
        UserName $userName
    ): void {
        $user = User::create($userId, $userName);

        if ($this->userRepository->byId($userId)) {
            throw new UserAlreadyExists($userId);
        }

        $this->userRepository->save($user);
    }
}
