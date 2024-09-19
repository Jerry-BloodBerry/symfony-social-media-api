<?php

namespace App\User\Api\Controller;

use App\Common\ClockInterface;
use App\Common\CQRS\CommandBusInterface;
use App\Common\CQRS\QueryBusInterface;
use App\Common\Response\ProblemDetails;
use App\Contract\Serializer\SerializerInterface;
use App\Contract\Validator\ValidatorInterface;
use App\Controller\BaseApiController;
use App\User\Api\Request\UpdateUserRequest;
use App\User\Application\Command\Message\RegisterUserMessage;
use App\User\Application\Query\Message\UserByIdQuery;
use App\User\Api\Request\RegisterUserRequest;
use App\User\Api\Response\GetSingleUserResponse;
use App\User\Application\Command\Message\DeleteUserMessage;
use App\User\Application\Command\Message\UpdateUserMessage;
use App\User\Domain\User;
use App\User\Exception\DuplicateEmailsNotAllowedException;
use App\User\Exception\DuplicateUsernamesNotAllowedException;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Ramsey\Uuid\Rfc4122\UuidV4;
use Ramsey\Uuid\Uuid;
use RegisterUserResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
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
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: RegisterUserRequest::class)))]
    #[OA\Response(
        response: 201,
        description: 'User created',
        content: new OA\JsonContent(ref: new Model(type: RegisterUserResponse::class))
    )]
    #[OA\Response(
        response: 409,
        description: 'Conflicting username and/or email',
        content: new OA\JsonContent(ref: new Model(type: ProblemDetails::class))
    )]
    #[OA\Tag(name: 'Users')]
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
        try {
            $this->commandBus->dispatch(
                new RegisterUserMessage(
                    $userId,
                    $userDto->username,
                    $userDto->email,
                    'https://www.gravatar.com/avatar', // TODO: Replace this with a real gravatar
                    \DateTime::createFromImmutable($this->clock->utcNow())
                )
            );
        } catch (HandlerFailedException $e) {
            $previous = $e->getPrevious();
            if ($previous instanceof DuplicateUsernamesNotAllowedException) {
                return $this->respondConflict(
                    message: 'User.DuplicateUsernameNotAllowed',
                    detail: 'Duplicate usernames are not allowed.',
                );
            } elseif ($previous instanceof DuplicateEmailsNotAllowedException) {
                return $this->respondConflict(
                    message: 'User.DuplicateEmailNotAllowed',
                    detail: 'Duplicate emails are not allowed.',
                );
            } else {
                throw $e;
            }
        }

        $user = $this->queryBus->handle(new UserByIdQuery($userId));
        $item = $this->serializer->serialize(RegisterUserResponse::fromUser($user), 'json');
        return $this->respondCreated($item, $this->generateUrl('user_get', ['id' => $userId->toString()]));
    }

    #[Route('/{id}', requirements: ['id' => Requirement::UUID_V4], name: 'user_get', methods: ['GET'])]
    #[OA\Parameter(name: 'id', in: 'path', schema: new OA\Schema(type: 'string', format: 'uuid'), description: 'User id')]
    #[OA\Response(
        response: 200,
        description: 'OK',
        content: new OA\JsonContent(ref: new Model(type: GetSingleUserResponse::class))
    )]
    #[OA\Response(
        response: 404,
        description: 'Not Found',
        content: new OA\JsonContent(ref: new Model(type: ProblemDetails::class))
    )]
    #[OA\Tag(name: 'Users')]
    public function get(string $id): JsonResponse
    {
        $user = $this->queryBus->handle(new UserByIdQuery(Uuid::fromString($id)));
        if (null == $user) {
            return $this->respondNotFound('User not found.');
        }
        $item = $this->serializer->serialize(GetSingleUserResponse::fromUser($user), 'json');
        return $this->respondWithItem($item);
    }

    #[Route('/{id}', requirements: ['id' => Requirement::UUID_V4], name: 'user_delete', methods: ['DELETE'])]
    #[OA\Parameter(name: 'id', in: 'path', schema: new OA\Schema(type: 'string', format: 'uuid'), description: 'User id')]
    #[OA\Response(
        response: 404,
        description: 'Not Found',
        content: new OA\JsonContent(ref: new Model(type: ProblemDetails::class))
    )]
    #[OA\Tag(name: 'Users')]
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
    #[OA\Parameter(name: 'id', in: 'path', schema: new OA\Schema(type: 'string', format: 'uuid'), description: 'User id')]
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: UpdateUserRequest::class)))]
    #[OA\Response(
        response: 204,
        description: 'User updated'
    )]
    #[OA\Response(
        response: 400,
        description: 'Validation Failed',
        content: new OA\JsonContent(ref: new Model(type: ProblemDetails::class))
    )]
    #[OA\Response(
        response: 404,
        description: 'Not Found',
        content: new OA\JsonContent(ref: new Model(type: ProblemDetails::class))
    )]
    #[OA\Response(
        response: 409,
        description: 'Conflicting username and/or email',
        content: new OA\JsonContent(ref: new Model(type: ProblemDetails::class))
    )]
    #[OA\Tag(name: 'Users')]
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
