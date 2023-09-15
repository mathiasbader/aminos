<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230915194304 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE base_scores CHANGE polar polar INT DEFAULT NULL, CHANGE charged charged INT DEFAULT NULL, CHANGE non_polar1 non_polar1 INT DEFAULT NULL, CHANGE non_polar2 non_polar2 INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE base_scores CHANGE non_polar1 non_polar1 INT NOT NULL, CHANGE non_polar2 non_polar2 INT NOT NULL, CHANGE polar polar INT NOT NULL, CHANGE charged charged INT NOT NULL');
    }
}
