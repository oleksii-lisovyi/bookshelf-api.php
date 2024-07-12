<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240712190936 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Implement full text search index for Author entity table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE INDEX author_fts_idx ON author USING GIN (to_tsvector('simple', firstname || ' ' || coalesce(middlename, '') || ' ' || lastname));");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX author_fts_idx');
    }
}
