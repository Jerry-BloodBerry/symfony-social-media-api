<?php

namespace App\Post\Api\Controller;

use App\Post\Api\Request\UpdatePostRequest;
use App\Post\Application\Query\Message\PostByIdQuery;
use App\Post\Application\Query\Message\PostsQuery;
use App\Common\CQRS\CommandBusInterface;
use App\Common\CQRS\QueryBusInterface;
use App\Common\ClockInterface;
use App\Common\Response\ProblemDetails;
use App\Contract\Serializer\SerializerInterface;
use App\Contract\Validator\ValidatorInterface;
use App\Controller\BaseApiController;
use App\Post\Application\Command\Message\CreatePostMessage;
use App\Post\Api\Request\CreatePostRequest;
use App\Post\Api\Response\ListPostResponse;
use App\Post\Api\Response\SinglePostResponse;
use App\Post\Application\Command\Message\DeletePostMessage;
use App\Post\Application\Command\Message\UpdatePostMessage;
use App\Post\Application\Query\Message\PostsByUserIdQuery;
use App\Post\Domain\Post;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Ramsey\Uuid\Rfc4122\UuidV4;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class PostController extends BaseApiController
{
    private readonly QueryBusInterface $queryBus;
    private readonly CommandBusInterface $commandBus;
    private readonly ClockInterface $clock;
    private readonly SerializerInterface $serializer;
    private readonly ValidatorInterface $validator;

    public function __construct(
        QueryBusInterface $queryBus,
        CommandBusInterface $commandBus,
        ClockInterface $clock,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->queryBus = $queryBus;
        $this->commandBus = $commandBus;
        $this->clock = $clock;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    #[Route('/api/post', methods: ['POST'], name: 'post_create')]
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: CreatePostRequest::class)))]
    #[OA\Response(
        response: 201,
        description: 'Create post',
        content: new OA\JsonContent(
            type: 'object',
            ref: new Model(type: SinglePostResponse::class)
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Validation Failed',
        content: new OA\JsonContent(ref: new Model(type: ProblemDetails::class))
    )]
    #[OA\Tag(name: 'Posts')]
    public function create(Request $request): JsonResponse
    {
        try {
            $postDto = $this->serializer->deserialize($request->getContent(), CreatePostRequest::class, 'json');
        } catch (\Exception $e) {
            return $this->respondBadRequest($e->getMessage());
        }
        try {
            $this->validator->validate($postDto);
        } catch (ValidationFailedException $e) {
            return $this->problemDetails($e->getViolations(), type: 'General/Post.ValidationFailed', title: 'Post data validation failed');
        }

        $postId = Uuid::uuid4();
        $this->commandBus->dispatch(
            new CreatePostMessage(
                $postId,
                Uuid::fromString($postDto->authorId),
                $postDto->content,
                $this->clock->utcNow()
            )
        );
        $post = $this->queryBus->handle(new PostByIdQuery($postId));
        $item = $this->serializer->serialize(SinglePostResponse::from($post), 'json');

        return $this->respondCreated($item, $this->generateUrl('post_get', ['id' => $postId->toString()]));
    }

    #[Route('/api/post/{id}', requirements: ['id' => Requirement::UUID_V4], name: 'post_get', methods: ['GET'])]
    #[OA\Parameter(name: 'id', in: 'path', schema: new OA\Schema(type: 'string', format: 'uuid'), description: 'Post id')]
    #[OA\Response(
        response: 200,
        description: 'Get post details',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: SinglePostResponse::class))
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Not Found',
        content: new OA\JsonContent(ref: new Model(type: ProblemDetails::class))
    )]
    #[OA\Tag(name: 'Posts')]
    public function get(string $id): JsonResponse
    {
        /** @var Post */
        $post = $this->queryBus->handle(new PostByIdQuery(Uuid::fromString($id)));
        if (null == $post) {
            return $this->respondNotFound('Post not found.');
        }
        $context = (new ObjectNormalizerContextBuilder())
            ->withGroups('post_details')
            ->toArray();
        $item = $this->serializer->serialize(SinglePostResponse::from($post), 'json', $context);

        return $this->respondWithItem($item);
    }

    #[Route('/api/post', name: 'post_get_all', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Get all posts',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: ListPostResponse::class))
        )
    )]
    #[OA\Tag(name: 'Posts')]
    public function getAll(
        #[MapQueryParameter] int $page = 1,
        #[MapQueryParameter] int $perPage = 30
    ): JsonResponse {
        $posts = $this->queryBus->handle(new PostsQuery($perPage, ($page - 1) * $perPage));
        $context = (new ObjectNormalizerContextBuilder())
            ->withGroups('post_list')
            ->toArray();
        $items = $this->serializer->serialize($posts, 'json', $context);

        return $this->respondWithCollection($items);
    }

    #[Route('/api/user/{id}/posts', requirements: ['id' => Requirement::UUID_V4], name: 'user_get_posts', methods: ['GET'])]
    #[OA\Parameter(name: 'id', in: 'path', schema: new OA\Schema(type: 'string', format: 'uuid'), description: 'Post id')]
    #[OA\Response(
        response: 200,
        description: 'Get user posts',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: ListPostResponse::class))
        )
    )]
    #[OA\Tag(name: 'Posts')]
    public function getUserPosts(
        string $id,
        #[MapQueryParameter] int $page = 1,
        #[MapQueryParameter] int $perPage = 30
    ): JsonResponse {
        $posts = $this->queryBus->handle(
            new PostsByUserIdQuery(Uuid::fromString($id), $perPage, ($page - 1) * $perPage)
        );
        $items = $this->serializer->serialize($posts, 'json');
        return $this->respondWithCollection($items);
    }

    #[Route('/api/post/{id}', requirements: ['id' => Requirement::UUID_V4], name: 'post_update', methods: ['PUT'])]
    #[OA\Parameter(name: 'id', in: 'path', schema: new OA\Schema(type: 'string', format: 'uuid'), description: 'Post id')]
    #[OA\Response(
        response: 204,
        description: 'Post updated'
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
    #[OA\Tag(name: 'Posts')]
    public function update(string $id, Request $request): JsonResponse
    {
        try {
            $postDto = $this->serializer->deserialize($request->getContent(), UpdatePostRequest::class, 'json');
        } catch (\Exception $e) {
            return $this->respondBadRequest($e->getMessage());
        }
        try {
            $this->validator->validate($postDto);
        } catch (ValidationFailedException $e) {
            return $this->problemDetails($e->getViolations(), type: 'General/Post.ValidationFailed', title: 'Post data validation failed');
        }
        $postId = UuidV4::fromString($id);
        $this->commandBus->dispatch(
            new UpdatePostMessage(
                $postId,
                $postDto->content
            )
        );

        return $this->respondNoContent();
    }

    #[Route('/api/post/{id}', requirements: ['id' => Requirement::UUID_V4], name: 'post_delete', methods: ['DELETE'])]
    #[OA\Parameter(name: 'id', in: 'path', schema: new OA\Schema(type: 'string', format: 'uuid'), description: 'Post id')]
    #[OA\Response(
        response: 204,
        description: 'Delete post'
    )]
    #[OA\Response(
        response: 404,
        description: 'Not Found',
        content: new OA\JsonContent(ref: new Model(type: ProblemDetails::class))
    )]
    #[OA\Tag(name: 'Posts')]
    public function delete(string $id): JsonResponse
    {
        $postId = UuidV4::fromString($id);
        $post = $this->queryBus->handle(new PostByIdQuery(Uuid::fromString($id)));
        if (null == $post) {
            return $this->respondNotFound('Post not found.');
        }
        $this->commandBus->dispatch(new DeletePostMessage($postId));
        return $this->respondNoContent();
    }
}
