<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230911185101 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds score before to test run entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE test_runs ADD score_before INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE test_runs DROP score_before');
    }
}
