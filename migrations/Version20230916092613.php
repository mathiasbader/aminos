<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230916092613 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE test_runs SET score_before = NULL');
        $this->addSql('ALTER TABLE test_runs CHANGE score_before score_before_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE test_runs ADD CONSTRAINT FK_854351D05590856E FOREIGN KEY (score_before_id) REFERENCES test_runs (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_854351D05590856E ON test_runs (score_before_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE test_runs DROP FOREIGN KEY FK_854351D05590856E');
        $this->addSql('DROP INDEX UNIQ_854351D05590856E ON test_runs');
        $this->addSql('ALTER TABLE test_runs CHANGE score_before_id score_before INT DEFAULT NULL');
    }
}
