<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240713123716 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add `updated_at` field for Book entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE book ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE book DROP updated_at');
    }
}
