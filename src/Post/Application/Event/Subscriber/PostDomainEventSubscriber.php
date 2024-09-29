<?php

namespace App\Post\Application\Event\Subscriber;

use App\Post\Application\Event\PostCreatedEvent;
use App\Post\Application\Event\PostUpdatedEvent;
use App\Post\Domain\PostCreatedDomainEvent;
use App\Post\Domain\PostUpdatedDomainEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class PostDomainEventSubscriber implements EventSubscriberInterface
{
    private readonly EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
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
        $this->eventDispatcher->dispatch(new PostCreatedEvent($event->postId), PostCreatedEvent::NAME);
    }

    public function onPostUpdated(PostUpdatedDomainEvent $event): void
    {
        $this->eventDispatcher->dispatch(new PostUpdatedEvent($event->postId), PostUpdatedEvent::NAME);
    }

}