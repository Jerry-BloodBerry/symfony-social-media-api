<?php

namespace App\User\Api\Controller;

use App\Common\ClockInterface;
use App\Post\Application\Query\Message\PostsByUserIdQuery;
use App\Common\CQRS\CommandBusInterface;
use App\Common\CQRS\QueryBusInterface;
use App\Contract\Serializer\SerializerInterface;
use App\Contract\Validator\ValidatorInterface;
use App\Controller\BaseApiController;
use App\User\Api\Request\UpdateUserRequest;
use App\User\Application\Command\Message\RegisterUserMessage;
use App\User\Application\Query\Message\UserByIdQuery;
use App\User\Api\Request\RegisterUserRequest;
use App\User\Application\Command\Message\DeleteUserMessage;
use App\User\Application\Command\Message\UpdateUserMessage;
use Ramsey\Uuid\Rfc4122\UuidV4;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[Route('/api/user')]
class UserController extends BaseApiController
{
    private readonly QueryBusInterface $queryBus;
    private readonly CommandBusInterface $commandBus;
    private readonly SerializerInterface $serializer;
    private readonly ValidatorInterface $validator;
    private readonly ClockInterface $clock;

    public function __construct(
        QueryBusInterface $queryBus,
        CommandBusInterface $commandBus,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        ClockInterface $clock
    ) {
        $this->queryBus = $queryBus;
        $this->commandBus = $commandBus;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->clock = $clock;
    }

    #[Route('', methods: ['POST'], name: 'user_create')]
    public function create(Request $request): JsonResponse
    {
        try {
            $userDto = $this->serializer->deserialize($request->getContent(), RegisterUserRequest::class, 'json');
        } catch (\Throwable $e) {
            return $this->respondBadRequest($e->getMessage());
        }
        try {
            $this->validator->validate($userDto);
        } catch (ValidationFailedException $e) {
            $serializedViolations = $this->serializer->serialize($e->getViolations(), 'json');
            return $this->respondBadRequest($serializedViolations);
        }
        $userId = Uuid::uuid4();
        $this->commandBus->dispatch(
            new RegisterUserMessage(
                $userId,
                $userDto->username,
                $userDto->email,
                'https://www.gravatar.com/avatar', // TODO: Replace this with a real gravatar
                new \DateTime('now')
            )
        );
        $user = $this->queryBus->handle(new UserByIdQuery($userId));
        $item = $this->serializer->serialize($user, 'json');
        return $this->respondCreated($item, $this->generateUrl('user_get', ['id' => $userId->toString()]));
    }

    #[Route('/{id}/posts', requirements: ['id' => Requirement::UUID_V4], name: 'user_get_posts', methods: ['GET'])]
    public function getUserPosts(
        string $id,
        #[MapQueryParameter] int $page = 1,
        #[MapQueryParameter] int $perPage = 30
    ): JsonResponse {
        $user = $this->queryBus->handle(new UserByIdQuery(Uuid::fromString($id)));
        if (null == $user) {
            return $this->respondNotFound('User not found.');
        }
        $posts = $this->queryBus->handle(
            new PostsByUserIdQuery(Uuid::fromString($id), $perPage, ($page - 1) * $perPage)
        );
        $items = $this->serializer->serialize($posts, 'json');
        return $this->respondWithCollection($items);
    }

    #[Route('/{id}', requirements: ['id' => Requirement::UUID_V4], name: 'user_get', methods: ['GET'])]
    public function get(string $id): JsonResponse
    {
        $user = $this->queryBus->handle(new UserByIdQuery(Uuid::fromString($id)));
        if (null == $user) {
            return $this->respondNotFound('User not found.');
        }
        $item = $this->serializer->serialize($user, 'json');
        return $this->respondWithItem($item);
    }

    #[Route('/{id}', requirements: ['id' => Requirement::UUID_V4], name: 'user_delete', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $userId = UuidV4::fromString($id);
        $user = $this->queryBus->handle(new UserByIdQuery(Uuid::fromString($id)));
        if (null == $user) {
            return $this->respondNotFound('User not found.');
        }
        $this->commandBus->dispatch(new DeleteUserMessage($userId));
        return $this->respondNoContent();
    }

    #[Route('/{id}', requirements: ['id' => Requirement::UUID_V4], name: 'user_update', methods: ['PUT'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $userId = UuidV4::fromString($id);
        $user = $this->queryBus->handle(new UserByIdQuery(Uuid::fromString($id)));
        if (null == $user) {
            return $this->respondNotFound('User not found.');
        }

        try {
            /** @var UpdateUserRequest */
            $userDto = $this->serializer->deserialize($request->getContent(), UpdateUserRequest::class, 'json');
        } catch (\Throwable $e) {
            return $this->respondBadRequest($e->getMessage());
        }
        try {
            $this->validator->validate($userDto);
        } catch (ValidationFailedException $e) {
            $serializedViolations = $this->serializer->serialize($e->getViolations(), 'json');
            return $this->respondBadRequest($serializedViolations);
        }
        $this->commandBus->dispatch(new UpdateUserMessage(
            $userId,
            $userDto->username,
            $userDto->email,
            $userDto->avatarUrl,
            \DateTime::createFromImmutable($this->clock->utcNow())
        ));
        return $this->respondNoContent();
    }
}
