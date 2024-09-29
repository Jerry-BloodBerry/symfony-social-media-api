<?php

namespace App\Post\Application\Service;

use Ramsey\Uuid\UuidInterface;

interface FriendFinderInterface
{
    /**
     * Gets the friends of the given author.
     * @param UuidInterface $authorId
     * @return array<Friend>
     */
    public function getFriendsOfAuthor(UuidInterface $authorId): array;
}