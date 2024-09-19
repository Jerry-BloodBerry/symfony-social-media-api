<?php

namespace App\Common\Event;

use App\Common\DomainEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class InMemoryDomainEventPublisher implements DomainEventPublisherInterface
{
    private EventDispatcherInterface $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function publish(DomainEvent $event): void
    {
        // Dispatch the event
        $this->dispatcher->dispatch($event, $event->name);
    }

    public function publishMany(DomainEvent ...$events): void
    {
        foreach ($events as $event) {
            $this->publish($event);
        }
    }
}