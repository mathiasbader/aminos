<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230917081125 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE test_runs DROP INDEX UNIQ_854351D05590856E, ADD INDEX IDX_854351D05590856E (score_before_id)');
        $this->addSql('DELETE FROM tests WHERE run_id <= 226');
        $this->addSql('DELETE FROM base_scores WHERE test_run_id <= 226');
        $this->addSql('DELETE FROM test_runs WHERE id <= 226');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE test_runs DROP INDEX IDX_854351D05590856E, ADD UNIQUE INDEX UNIQ_854351D05590856E (score_before_id)');
    }
}
