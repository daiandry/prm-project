<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210107073513 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prm_user ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE prm_user ADD CONSTRAINT FK_107BC230B03A8386 FOREIGN KEY (created_by_id) REFERENCES prm_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_107BC230B03A8386 ON prm_user (created_by_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE prm_user DROP CONSTRAINT FK_107BC230B03A8386');
        $this->addSql('DROP INDEX IDX_107BC230B03A8386');
        $this->addSql('ALTER TABLE prm_user DROP created_by_id');
    }
}
