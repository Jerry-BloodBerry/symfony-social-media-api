<?php

namespace App\Post\Api\Controller;

use App\Post\Application\Query\Message\PostByIdQuery;
use App\Post\Application\Query\Message\PostsQuery;
use App\Common\CQRS\CommandBusInterface;
use App\Common\CQRS\QueryBusInterface;
use App\Common\ClockInterface;
use App\Contract\Serializer\SerializerInterface;
use App\Contract\Validator\ValidatorInterface;
use App\Controller\BaseApiController;
use App\Post\Application\Command\Message\CreatePostMessage;
use App\Post\Api\Request\CreatePostRequest;
use App\Post\Domain\Post;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[Route('/api/post')]
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

    #[Route('', methods: ['POST'], name: 'post_create')]
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

        $context = (new ObjectNormalizerContextBuilder())
            ->withGroups('post_details')
            ->toArray();
        $item = $this->serializer->serialize($post, 'json', $context);
        return $this->respondCreated($item, $this->generateUrl('post_get', ['id' => $postId->toString()]));
    }

    #[Route('/{id}', requirements: ['id' => Requirement::UUID_V4], name: 'post_get', methods: ['GET'])]
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
        $item = $this->serializer->serialize($post, 'json', $context);
        return $this->respondWithItem($item);
    }

    #[Route('', name: 'post_get_all', methods: ['GET'])]
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
}
