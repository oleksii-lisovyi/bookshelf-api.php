<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240712085842 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Book entity table and Book to Author relation table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<END
            CREATE TABLE IF NOT EXISTS book (
                id SERIAL PRIMARY KEY, 
                name VARCHAR(1000) NOT NULL, 
                short_description TEXT DEFAULT NULL, 
                image VARCHAR(255) DEFAULT NULL, 
                published_at DATE DEFAULT NULL
            );
END
        );
        $this->addSql(<<<END
            CREATE TABLE IF NOT EXISTS book_author (
                book_id INT NOT NULL, 
                author_id INT NOT NULL, 
                PRIMARY KEY(book_id, author_id),
                CONSTRAINT FK_9478D34516A2B381 FOREIGN KEY (book_id) REFERENCES book (id) 
                    ON DELETE CASCADE DEFERRABLE INITIALLY IMMEDIATE,
                CONSTRAINT FK_9478D345F675F31B FOREIGN KEY (author_id) REFERENCES author (id) 
                    ON DELETE CASCADE DEFERRABLE INITIALLY IMMEDIATE
            );
END
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE book_author DROP CONSTRAINT FK_9478D34516A2B381');
        $this->addSql('ALTER TABLE book_author DROP CONSTRAINT FK_9478D345F675F31B');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE book_author');
    }
}
