<?php

namespace App\Post\Infrastructure\Mercure;

use App\Post\Application\Event\PostCreatedEvent;
use App\Post\Application\Event\PostUpdatedEvent;
use App\Post\Application\Service\FriendFinderInterface;
use App\Post\Domain\PostRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class MercurePostCreatedEventSubscriber implements EventSubscriberInterface
{
    private readonly HubInterface $hub;
    private readonly PostRepositoryInterface $postRepository;
    private readonly FriendFinderInterface $friendFinder;

    public function __construct(HubInterface $hub, PostRepositoryInterface $postRepository, FriendFinderInterface $friendFinder)
    {
        $this->hub = $hub;
        $this->postRepository = $postRepository;
        $this->friendFinder = $friendFinder;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PostCreatedEvent::NAME => 'onPostCreated',
            PostUpdatedEvent::NAME => 'onPostUpdated',
        ];
    }

    public function onPostCreated(PostCreatedEvent $event): void
    {
        $post = $this->postRepository->find($event->postId);
        $author = $post->getAuthor();
        if ($post) {
            $friends = $this->friendFinder->getFriendsOfAuthor($author->getId());
            foreach ($friends as $friend) {
                $topic = sprintf('/friends/%s/new_posts', $friend->id);
                $update = new Update(
                    $topic,
                    json_encode([
                        'user' => [
                            'id' => $author->getId(),
                            'username' => $author->getName()
                        ],
                        'post' => [
                            'id' => $post->getId(),
                            'shortContent' => substr($post->getContent(), 0, 50),
                            'createdAt' => $post->getCreatedAt()->format('Y-m-d H:i:s')
                        ]
                    ])
                );
                $this->hub->publish($update);
            }

        }

    }
}