<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230915171248 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE base_scores (' .
            '    id          INT AUTO_INCREMENT NOT NULL, ' .
            '    test_run_id INT DEFAULT NULL           , ' .
            '    un_polar1   INT                NOT NULL, ' .
            '    un_polar2   INT                NOT NULL, ' .
            '    polar       INT                NOT NULL, ' .
            '    charged     INT                NOT NULL, ' .
            '    UNIQUE INDEX UNIQ_6F30AF37133AF9EA (test_run_id), ' .
            '    PRIMARY KEY(id)' .
            ') DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'ALTER TABLE base_scores ' .
            '    ADD CONSTRAINT FK_6F30AF37133AF9EA FOREIGN KEY (test_run_id) REFERENCES test_runs (id)'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE base_scores DROP FOREIGN KEY FK_6F30AF37133AF9EA');
        $this->addSql('DROP TABLE base_scores');
    }
}
