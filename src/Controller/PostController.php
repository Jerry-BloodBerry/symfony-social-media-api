<?php

namespace App\Controller;

use App\Application\Post\Command\Message\CreatePostMessage;
use App\Application\Post\Query\Message\PostByIdQuery;
use App\Application\Post\Query\Message\PostsQuery;
use App\Application\Service\CQRS\CommandBusInterface;
use App\Application\Service\CQRS\QueryBusInterface;
use App\Application\Transformer\PostTransformer;
use App\Request\Post\CreatePostRequest;
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

    public function __construct(
        QueryBusInterface $queryBus,
        CommandBusInterface $commandBus,
    ) {
        parent::__construct();
        $this->queryBus = $queryBus;
        $this->commandBus = $commandBus;
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
                new \DateTime('now')
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
