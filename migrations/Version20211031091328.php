<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211031091328 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tests ADD answer_amino_id INT DEFAULT NULL AFTER answer');
        $this->addSql('ALTER TABLE tests ADD CONSTRAINT FK_1260FC5E47F43FC5 FOREIGN KEY (answer_amino_id) REFERENCES aminoacids (id)');
        $this->addSql('CREATE INDEX IDX_1260FC5E47F43FC5 ON tests (answer_amino_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tests DROP FOREIGN KEY FK_1260FC5E47F43FC5');
        $this->addSql('DROP INDEX IDX_1260FC5E47F43FC5 ON tests');
        $this->addSql('ALTER TABLE tests DROP answer_amino_id');
    }
}
