<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211018193244 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('RENAME TABLE user TO users');
        $this->addSql('CREATE TABLE test_runs (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, started DATETIME NOT NULL, completed DATETIME DEFAULT NULL, INDEX IDX_854351D0A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tests (id INT AUTO_INCREMENT NOT NULL, run_id INT NOT NULL, amino_id INT NOT NULL, type INT DEFAULT NULL, correct TINYINT(1) DEFAULT NULL, answer VARCHAR(255) DEFAULT NULL, answered DATETIME DEFAULT NULL, INDEX IDX_1260FC5E84E3FEC4 (run_id), INDEX IDX_1260FC5E60BE3EA0 (amino_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE test_runs ADD CONSTRAINT FK_854351D0A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE tests ADD CONSTRAINT FK_1260FC5E84E3FEC4 FOREIGN KEY (run_id) REFERENCES test_runs (id)');
        $this->addSql('ALTER TABLE tests ADD CONSTRAINT FK_1260FC5E60BE3EA0 FOREIGN KEY (amino_id) REFERENCES aminoacids (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tests DROP FOREIGN KEY FK_1260FC5E84E3FEC4');
        $this->addSql('ALTER TABLE test_runs DROP FOREIGN KEY FK_854351D0A76ED395');
        $this->addSql('DROP TABLE test_runs');
        $this->addSql('DROP TABLE tests');
        $this->addSql('RENAME TABLE users TO user');
    }
}
