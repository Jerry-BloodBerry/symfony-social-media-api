<?php

namespace App\Post\Api\Controller;

use App\Post\Application\Query\Message\PostByIdQuery;
use App\Post\Application\Query\Message\PostsQuery;
use App\Common\CQRS\CommandBusInterface;
use App\Common\CQRS\QueryBusInterface;
use App\Common\ClockInterface;
use App\Controller\BaseApiController;
use App\Post\Api\Transformer\PostTransformer;
use App\Post\Application\Command\Message\CreatePostMessage;
use App\Post\Api\Request\CreatePostRequest;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/api/post')]
class PostController extends BaseApiController
{
    private readonly QueryBusInterface $queryBus;
    private readonly CommandBusInterface $commandBus;
    private readonly ClockInterface $clock;

    public function __construct(
        QueryBusInterface $queryBus,
        CommandBusInterface $commandBus,
        ClockInterface $clock
    ) {
        parent::__construct();
        $this->queryBus = $queryBus;
        $this->commandBus = $commandBus;
        $this->clock = $clock;
    }

    #[Route('', methods: ['POST'], name: 'post_create')]
    public function create(#[MapRequestPayload(acceptFormat: 'json')] CreatePostRequest $request): JsonResponse
    {
        $postId = Uuid::uuid4();
        $this->commandBus->dispatch(
            new CreatePostMessage(
                $postId,
                Uuid::fromString($request->authorId),
                $request->content,
                $this->clock->utcNow()
            )
        );
        $post = $this->queryBus->handle(new PostByIdQuery($postId));

        return $this->respondWithCreated($post, new PostTransformer(), 'posts');
    }

    #[Route('/{id}', requirements: ['id' => Requirement::UUID_V4], name: 'post_get', methods: ['GET'])]
    public function get(string $id): JsonResponse
    {
        $post = $this->queryBus->handle(new PostByIdQuery(Uuid::fromString($id)));
        if (null == $post) {
            return $this->respondWithNotFound('Post not found.');
        }
        return $this->respondWithItem($post, new PostTransformer(), 'posts');
    }

    #[Route('', name: 'post_get_all', methods: ['GET'])]
    public function getAll(
    #[MapQueryParameter] int $page = 1,
    #[MapQueryParameter] int $perPage = 30
    ): JsonResponse {
        $posts = $this->queryBus->handle(new PostsQuery($perPage, ($page - 1) * $perPage));

        return $this->respondWithCollection($posts, new PostTransformer(), 'posts');
    }
}
