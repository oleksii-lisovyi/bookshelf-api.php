<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240716080306 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Set `book`.`updated_at` column to be nullable';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE book ALTER COLUMN updated_at DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE book ALTER COLUMN updated_at SET NOT NULL');
    }
}
