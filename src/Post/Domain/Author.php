<?php

namespace App\Post\Domain;

use Ramsey\Uuid\UuidInterface;

class Author
{
    private UuidInterface $id;
    private string $name;

    public function __construct(UuidInterface $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}