<?php

namespace App\User\Infrastructure;

use App\Post\Application\Service\Friend;
use App\Post\Application\Service\FriendFinderInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class FriendFinder implements FriendFinderInterface
{

    public function getFriendsOfAuthor(UuidInterface $authorId): array
    {
        // TODO: Implement getFriendsOfAuthor() method properly.
        return [
            new Friend(Uuid::uuid4(), 'friend1'),
            new Friend(Uuid::uuid4(), 'friend2'),
            new Friend(Uuid::uuid4(), 'friend3'),
        ];
    }
}