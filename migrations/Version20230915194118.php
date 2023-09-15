<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230915194118 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE base_scores ADD non_polar1 INT NOT NULL, ADD non_polar2 INT NOT NULL, DROP un_polar1, DROP un_polar2');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE base_scores ADD un_polar1 INT NOT NULL, ADD un_polar2 INT NOT NULL, DROP non_polar1, DROP non_polar2');
    }
}
