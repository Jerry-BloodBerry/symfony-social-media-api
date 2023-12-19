<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Create posts table.
 */
final class Version20231219201953 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create posts table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE posts(
            "id" SERIAL PRIMARY KEY,
            "uuid" uuid NOT NULL,
            "author_id" uuid NOT NULL,
            "content" TEXT NOT NULL,
            "created_at" timestamp NOT NULL,
            "updated_at" timestamp NULL,
            FOREIGN KEY (author_id) REFERENCES users("uuid")
        );');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE posts;');
    }
}
