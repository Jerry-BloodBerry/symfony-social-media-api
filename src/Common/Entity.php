<?php

namespace App\Common;

use Ramsey\Uuid\UuidInterface;

abstract class Entity
{

    /**
     * @var array<DomainEvent>
     */
    private array $domainEvents = [];

    public function __construct(
        public readonly UuidInterface $id
    ) {
    }

    public function getDomainEvents(): array
    {
        return $this->domainEvents;
    }

    protected function raise(DomainEvent $event): void
    {
        $this->domainEvents[] = $event;
    }
}