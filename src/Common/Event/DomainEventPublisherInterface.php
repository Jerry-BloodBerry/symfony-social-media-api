<?php

namespace App\Common\Event;

use App\Common\DomainEvent;

interface DomainEventPublisherInterface
{
    public function publish(DomainEvent $event): void;
    public function publishMany(DomainEvent ...$events): void;
}