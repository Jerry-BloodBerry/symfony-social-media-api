<?php

namespace App\Post\Application\Service;

use Ramsey\Uuid\UuidInterface;

class Friend
{
    public function __construct(
        public readonly UuidInterface $id,
        public readonly string $username
    ) {
    }
}