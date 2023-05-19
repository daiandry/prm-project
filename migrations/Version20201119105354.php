<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201119105354 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE trace_log_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE topology.topology_id_seq CASCADE');
        $this->addSql('DROP TABLE topology.topology');
        $this->addSql('DROP TABLE topology.layer');
        $this->addSql('DROP TABLE trace_log');
        $this->addSql('ALTER TABLE prm_documents ADD enabled BOOLEAN DEFAULT \'true\'');
        $this->addSql('ALTER TABLE prm_projet ADD date_create TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE prm_projet ADD date_modify TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE prm_projet ALTER en_retard SET DEFAULT \'false\'');
        $this->addSql('ALTER TABLE prm_projet ALTER inaugurable SET DEFAULT \'false\'');
        $this->addSql('ALTER TABLE prm_statut_projet ADD couleur VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE prm_taches ADD unite_indicateur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE prm_taches ADD unite_monetaire_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE prm_taches ADD CONSTRAINT FK_DFD546B2AEC75ECB FOREIGN KEY (unite_indicateur_id) REFERENCES prm_unite_indicateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prm_taches ADD CONSTRAINT FK_DFD546B2A6177BC9 FOREIGN KEY (unite_monetaire_id) REFERENCES prm_unite_monetaire (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_DFD546B2AEC75ECB ON prm_taches (unite_indicateur_id)');
        $this->addSql('CREATE INDEX IDX_DFD546B2A6177BC9 ON prm_taches (unite_monetaire_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SCHEMA topology');
        $this->addSql('CREATE SEQUENCE trace_log_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE topology.topology_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE topology.topology (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, srid INT NOT NULL, "precision" DOUBLE PRECISION NOT NULL, hasz BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX topology_name_key ON topology.topology (name)');
        $this->addSql('CREATE TABLE topology.layer (topology_id INT NOT NULL, layer_id INT NOT NULL, schema_name VARCHAR(255) NOT NULL, table_name VARCHAR(255) NOT NULL, feature_column VARCHAR(255) NOT NULL, feature_type INT NOT NULL, level INT DEFAULT 0 NOT NULL, child_id INT DEFAULT NULL, PRIMARY KEY(topology_id, layer_id))');
        $this->addSql('CREATE UNIQUE INDEX layer_schema_name_table_name_feature_column_key ON topology.layer (schema_name, table_name, feature_column)');
        $this->addSql('CREATE INDEX IDX_181D8D68ED697DD5 ON topology.layer (topology_id)');
        $this->addSql('CREATE TABLE trace_log (id INT NOT NULL, user_id INT DEFAULT NULL, classe_name VARCHAR(255) DEFAULT NULL, metadata TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_b4507c21a76ed395 ON trace_log (user_id)');
        $this->addSql('COMMENT ON COLUMN trace_log.metadata IS \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE topology.layer ADD CONSTRAINT layer_topology_id_fkey FOREIGN KEY (topology_id) REFERENCES topology (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE trace_log ADD CONSTRAINT fk_b4507c21a76ed395 FOREIGN KEY (user_id) REFERENCES prm_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prm_documents DROP enabled');
        $this->addSql('ALTER TABLE prm_projet DROP date_create');
        $this->addSql('ALTER TABLE prm_projet DROP date_modify');
        $this->addSql('ALTER TABLE prm_projet ALTER inaugurable DROP DEFAULT');
        $this->addSql('ALTER TABLE prm_projet ALTER en_retard DROP DEFAULT');
        $this->addSql('ALTER TABLE prm_taches DROP CONSTRAINT FK_DFD546B2AEC75ECB');
        $this->addSql('ALTER TABLE prm_taches DROP CONSTRAINT FK_DFD546B2A6177BC9');
        $this->addSql('DROP INDEX IDX_DFD546B2AEC75ECB');
        $this->addSql('DROP INDEX IDX_DFD546B2A6177BC9');
        $this->addSql('ALTER TABLE prm_taches DROP unite_indicateur_id');
        $this->addSql('ALTER TABLE prm_taches DROP unite_monetaire_id');
        $this->addSql('ALTER TABLE prm_statut_projet DROP couleur');
    }
}
