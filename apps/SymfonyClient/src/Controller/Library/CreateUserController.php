<?php

declare(strict_types=1);

namespace SymfonyClient\Controller\Library;

use Library\User\Application\CreateUser\CreateUserCommand;
use Library\User\Domain\Exception\UserAlreadyExists;
use Shared\Domain\Exception\InvalidUuid;
use Shared\Domain\ValueObject\Uuid;
use Shared\Infrastructure\Symfony\Controller\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function sprintf;

final class CreateUserController extends ApiController
{
    public function __invoke(Request $request): Response
    {
        $payload = $this->getPayload($request);
        $userId = Uuid::random()->value();

        $this->dispatch(
            new CreateUserCommand($userId, $payload['name'])
        );

        return $this->createApiResponse(
            sprintf(
                'User created with id: %s',
                $userId
            ),
            Response::HTTP_CREATED
        );
    }

    protected function exceptions(): array
    {
        return [
            InvalidUuid::class => Response::HTTP_BAD_REQUEST,
            UserAlreadyExists::class => Response::HTTP_BAD_REQUEST,
        ];
    }
}
