<?php

namespace App\Controller;

use App\Application\Service\CQRS\CommandBusInterface;
use App\Application\Service\CQRS\QueryBusInterface;
use App\Application\Transformer\UserTransformer;
use App\Application\User\Command\Message\CreateUserMessage;
use App\Application\User\Query\Message\UserByIdQuery;
use App\Request\User\CreateUserRequest;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/api/user')]
class UserController extends BaseApiController
{
    private readonly QueryBusInterface $queryBus;
    private readonly CommandBusInterface $commandBus;

    public function __construct(
        QueryBusInterface $queryBus,
        CommandBusInterface $commandBus,
    ) {
        parent::__construct();
        $this->queryBus = $queryBus;
        $this->commandBus = $commandBus;
    }

    #[Route('', methods: ['POST'], name: 'user_create')]
    public function create(#[MapRequestPayload(acceptFormat: 'json')] CreateUserRequest $request): JsonResponse
    {
        $userId = Uuid::uuid4();
        $this->commandBus->dispatch(
            new CreateUserMessage(
                $userId,
                $request->username,
                $request->email,
                'https://www.gravatar.com/avatar', // TODO: Replace this with a real gravatar
                new \DateTime('now')
            )
        );
        $user = $this->queryBus->handle(new UserByIdQuery($userId));

        return $this->respondWithCreated($user, new UserTransformer(), 'users');
    }

    #[Route('/{id}/posts', requirements: ['id' => Requirement::UUID_V4], name: 'user_get_posts', methods: ['GET'])]
    public function getUserPosts(string $id)
    {

    }

    #[Route('/{id}', requirements: ['id' => Requirement::UUID_V4], name: 'user_get', methods: ['GET'])]
    public function get(string $id): JsonResponse
    {
        $user = $this->queryBus->handle(new UserByIdQuery(Uuid::fromString($id)));
        if (null == $user) {
            return $this->respondWithNotFound('User not found.');
        }
        return $this->respondWithItem($user, new UserTransformer(), 'users');
    }
}
