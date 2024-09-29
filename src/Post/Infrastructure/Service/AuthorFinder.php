<?php

namespace App\Post\Infrastructure\Service;

use App\Post\Application\Service\AuthorFinderInterface;
use App\Post\Domain\Author;
use App\Post\Infrastructure\Table\AuthorsTable;
use Doctrine\DBAL\Connection;
use Ramsey\Uuid\Rfc4122\UuidV4;
use Ramsey\Uuid\UuidInterface;

class AuthorFinder implements AuthorFinderInterface
{
    private readonly Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function find(UuidInterface $id): ?Author
    {
        $result = $this->connection->createQueryBuilder()
            ->select(AuthorsTable::ID, AuthorsTable::USERNAME)
            ->from(AuthorsTable::TABLE_NAME)
            ->where(AuthorsTable::ID . ' = ?')
            ->setParameter(1, $id->toString())
            ->executeQuery()
            ->fetchAssociative();
        if (!$result) {
            return null;
        }
        return new Author(UuidV4::fromString($result[AuthorsTable::ID]), $result[AuthorsTable::USERNAME]);
    }
}