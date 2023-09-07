<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230907101834 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add score to test run entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE test_runs ADD score INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE test_runs DROP score');
    }
}
