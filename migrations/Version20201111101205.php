<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201111101205 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE topology.topology_id_seq CASCADE');
        $this->addSql('DROP TABLE topology.topology');
        $this->addSql('DROP TABLE topology.layer');
        $this->addSql('ALTER TABLE prm_profil ADD status BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE prm_projet_zone ADD CONSTRAINT FK_61004F3AC18272 FOREIGN KEY (projet_id) REFERENCES prm_zone_geo (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prm_user ADD CONSTRAINT FK_107BC23098260155 FOREIGN KEY (region_id) REFERENCES prm_zone_geo (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prm_zone_geo ADD CONSTRAINT FK_4810ABF7C54C8C93 FOREIGN KEY (type_id) REFERENCES prm_type_zone (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SCHEMA topology');
        $this->addSql('CREATE SEQUENCE topology.topology_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE topology.topology (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, srid INT NOT NULL, "precision" DOUBLE PRECISION NOT NULL, hasz BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX topology_name_key ON topology.topology (name)');
        $this->addSql('CREATE TABLE topology.layer (topology_id INT NOT NULL, layer_id INT NOT NULL, schema_name VARCHAR(255) NOT NULL, table_name VARCHAR(255) NOT NULL, feature_column VARCHAR(255) NOT NULL, feature_type INT NOT NULL, level INT DEFAULT 0 NOT NULL, child_id INT DEFAULT NULL, PRIMARY KEY(topology_id, layer_id))');
        $this->addSql('CREATE UNIQUE INDEX layer_schema_name_table_name_feature_column_key ON topology.layer (schema_name, table_name, feature_column)');
        $this->addSql('CREATE INDEX IDX_181D8D68ED697DD5 ON topology.layer (topology_id)');
        $this->addSql('ALTER TABLE topology.layer ADD CONSTRAINT layer_topology_id_fkey FOREIGN KEY (topology_id) REFERENCES topology (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prm_profil DROP status');
        $this->addSql('ALTER TABLE prm_projet_zone DROP CONSTRAINT FK_61004F3AC18272');
        $this->addSql('ALTER TABLE prm_user DROP CONSTRAINT FK_107BC23098260155');
        $this->addSql('ALTER TABLE prm_zone_geo DROP CONSTRAINT FK_4810ABF7C54C8C93');
    }
}
