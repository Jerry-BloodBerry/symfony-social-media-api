<?php

namespace App\Post\Application\Service;

use App\Post\Domain\Author;
use Ramsey\Uuid\UuidInterface;

interface AuthorFinderInterface
{
    public function find(UuidInterface $id): ?Author;
}