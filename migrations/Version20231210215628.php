<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Create users table.
 */
final class Version20231210215628 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create users table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE users(
            "id" SERIAL PRIMARY KEY,
            "uuid" uuid NOT NULL UNIQUE,
            "username" varchar(255) NOT NULL UNIQUE,
            "email" varchar(255) NOT NULL UNIQUE,
            "avatar_url" text NOT NULL,
            "created_at" timestamp NOT NULL,
            "updated_at" timestamp NULL
        );');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE users;');
    }
}
