<?php

namespace App\Post\Application\Event\Subscriber;

use App\Post\Domain\PostCreatedDomainEvent;
use App\Post\Domain\PostUpdatedDomainEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PostDomainEventSubscriber implements EventSubscriberInterface
{
    private readonly LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PostCreatedDomainEvent::NAME => 'onPostCreated',
            PostUpdatedDomainEvent::NAME => 'onPostUpdated',
        ];
    }

    public function onPostCreated(PostCreatedDomainEvent $event): void
    {
        $this->logger->info("Post created with id: {$event->postId}");
    }

    public function onPostUpdated(PostUpdatedDomainEvent $event): void
    {
        $this->logger->info("Post updated with id: {$event->postId}");
    }

}