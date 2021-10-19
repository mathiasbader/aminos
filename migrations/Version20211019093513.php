<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211019093513 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE test_aminoacid (test_id INT NOT NULL, aminoacid_id INT NOT NULL, INDEX IDX_643BB4D91E5D0459 (test_id), INDEX IDX_643BB4D9F93DA943 (aminoacid_id), PRIMARY KEY(test_id, aminoacid_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE test_aminoacid ADD CONSTRAINT FK_643BB4D91E5D0459 FOREIGN KEY (test_id) REFERENCES tests (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE test_aminoacid ADD CONSTRAINT FK_643BB4D9F93DA943 FOREIGN KEY (aminoacid_id) REFERENCES aminoacids (id) ON DELETE CASCADE');
        $this->addSql('DROP INDEX uniq_8d93d649e7927c74 ON users');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('DROP INDEX uniq_8d93d64977153098 ON users');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E977153098 ON users (code)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE test_aminoacid');
        $this->addSql('DROP INDEX uniq_1483a5e9e7927c74 ON users');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON users (email)');
        $this->addSql('DROP INDEX uniq_1483a5e977153098 ON users');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64977153098 ON users (code)');
    }
}
