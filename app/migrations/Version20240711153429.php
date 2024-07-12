<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240711153429 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Author entity table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<END
            CREATE TABLE IF NOT EXISTS author (
                id SERIAL PRIMARY KEY,
                firstname VARCHAR(255) NOT NULL,
                lastname VARCHAR(255) NOT NULL, 
                middlename VARCHAR(255) DEFAULT NULL 
            );
END
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE author');
    }
}
