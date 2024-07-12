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
        $this->addSql('CREATE SEQUENCE book_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql(<<<END
            CREATE TABLE book (
                id SERIAL PRIMARY KEY, 
                name VARCHAR(1000) NOT NULL, 
                short_description TEXT DEFAULT NULL, 
                image VARCHAR(255) DEFAULT NULL, 
                published_at DATE DEFAULT NULL
            );
END
        );
        $this->addSql(<<<END
            CREATE TABLE book_author (
                book_id INT NOT NULL, 
                author_id INT NOT NULL, 
                PRIMARY KEY(book_id, author_id),
                CONSTRAINT IDX_9478D34516A2B381 FOREIGN KEY (book_id) REFERENCES book (id) 
                    ON DELETE CASCADE DEFERRABLE INITIALLY IMMEDIATE,
                CONSTRAINT IDX_9478D345F675F31B FOREIGN KEY (author_id) REFERENCES author (id) 
                    ON DELETE CASCADE DEFERRABLE INITIALLY IMMEDIATE
            );
END
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE book_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE author_id_seq1 INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE book_author DROP CONSTRAINT FK_9478D34516A2B381');
        $this->addSql('ALTER TABLE book_author DROP CONSTRAINT FK_9478D345F675F31B');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE book_author');
        $this->addSql('CREATE SEQUENCE author_id_seq');
        $this->addSql('SELECT setval(\'author_id_seq\', (SELECT MAX(id) FROM author))');
        $this->addSql('ALTER TABLE author ALTER id SET DEFAULT nextval(\'author_id_seq\')');
    }
}
