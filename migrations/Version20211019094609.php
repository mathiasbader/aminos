<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211019094609 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE test_run_aminoacid (test_run_id INT NOT NULL, aminoacid_id INT NOT NULL, INDEX IDX_5D298A3133AF9EA (test_run_id), INDEX IDX_5D298A3F93DA943 (aminoacid_id), PRIMARY KEY(test_run_id, aminoacid_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE test_run_aminoacid ADD CONSTRAINT FK_5D298A3133AF9EA FOREIGN KEY (test_run_id) REFERENCES test_runs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE test_run_aminoacid ADD CONSTRAINT FK_5D298A3F93DA943 FOREIGN KEY (aminoacid_id) REFERENCES aminoacids (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE test_runs DROP aminos');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE test_run_aminoacid');
        $this->addSql('ALTER TABLE test_runs ADD aminos LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
    }
}
