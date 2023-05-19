<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201027125443 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE lim_dist_gid_seq CASCADE');
        $this->addSql('DROP SEQUENCE lim_com201118_gid_seq CASCADE');
        $this->addSql('DROP SEQUENCE prmp_projet_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE prm_type_secteur_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE topology.topology_id_seq CASCADE');
        $this->addSql('DROP TABLE topology.topology');
        $this->addSql('DROP TABLE topology.layer');
        $this->addSql('DROP TABLE prm_type_secteur');
        $this->addSql('DROP TABLE lim_dist');
        $this->addSql('DROP TABLE lim_com201118');
        $this->addSql('DROP TABLE prmp_projet');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SCHEMA topology');
        $this->addSql('CREATE SEQUENCE lim_dist_gid_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE lim_com201118_gid_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE prmp_projet_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE prm_type_secteur_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE topology.topology_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE topology.topology (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, srid INT NOT NULL, "precision" DOUBLE PRECISION NOT NULL, hasz BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX topology_name_key ON topology.topology (name)');
        $this->addSql('CREATE TABLE topology.layer (topology_id INT NOT NULL, layer_id INT NOT NULL, schema_name VARCHAR(255) NOT NULL, table_name VARCHAR(255) NOT NULL, feature_column VARCHAR(255) NOT NULL, feature_type INT NOT NULL, level INT DEFAULT 0 NOT NULL, child_id INT DEFAULT NULL, PRIMARY KEY(topology_id, layer_id))');
        $this->addSql('CREATE UNIQUE INDEX layer_schema_name_table_name_feature_column_key ON topology.layer (schema_name, table_name, feature_column)');
        $this->addSql('CREATE INDEX IDX_181D8D68ED697DD5 ON topology.layer (topology_id)');
        $this->addSql('CREATE TABLE prm_type_secteur (id INT NOT NULL, libelle VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE lim_dist (gid SERIAL NOT NULL, c_dst SMALLINT DEFAULT NULL, region VARCHAR(50) DEFAULT NULL, district VARCHAR(50) DEFAULT NULL, c_rg INT DEFAULT NULL, geom geometry(GEOMETRY, 0) DEFAULT NULL, PRIMARY KEY(gid))');
        $this->addSql('CREATE INDEX lim_dist_geom_idx ON lim_dist (geom)');
        $this->addSql('CREATE TABLE lim_com201118 (gid SERIAL NOT NULL, nom_region VARCHAR(40) DEFAULT NULL, nom_distri VARCHAR(40) DEFAULT NULL, nom_commun VARCHAR(30) DEFAULT NULL, c_com INT DEFAULT NULL, c_dst SMALLINT DEFAULT NULL, c_rg SMALLINT DEFAULT NULL, geom geometry(GEOMETRY, 0) DEFAULT NULL, PRIMARY KEY(gid))');
        $this->addSql('CREATE INDEX lim_com201118_geom_idx ON lim_com201118 (geom)');
        $this->addSql('CREATE TABLE prmp_projet (id INT NOT NULL, secteur_id INT DEFAULT NULL, engagement_id INT DEFAULT NULL, type_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, conv_cl VARCHAR(255) NOT NULL, projet_parent_id INT NOT NULL, collectivite_territoriale_descentralisee VARCHAR(255) NOT NULL, secteur_activite VARCHAR(255) NOT NULL, pdm_date_fin_appel_offre TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, pdm_date_signature_prevu TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, pdm_date_signature_reel TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, pdm_titulaire_du_marche VARCHAR(255) NOT NULL, pdm_designation VARCHAR(255) NOT NULL, pdm_tiers_nif VARCHAR(255) NOT NULL, pdm_notification_et_ordre_service VARCHAR(255) NOT NULL, pdm_date_export_siig TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, pdm_date_lancement_travaux_prevu TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, pdm_date_travaux_reel TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, pdm_delai_execution_prevu TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, pdm_date_fin_prevu TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, rf_autorisation_engagement VARCHAR(255) NOT NULL, situation_actuelle_marche_public_id INT NOT NULL, observation VARCHAR(255) NOT NULL, priorite VARCHAR(255) NOT NULL, soa_code VARCHAR(255) NOT NULL, pcop_compte VARCHAR(255) NOT NULL, categorie VARCHAR(255) NOT NULL, coordonnee_geographique_projet VARCHAR(255) NOT NULL, promesse_presidentielle VARCHAR(255) NOT NULL, inaugurable VARCHAR(255) NOT NULL, en_retard VARCHAR(255) NOT NULL, date_inauguration TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_by INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_522fe58d9f7e4405 ON prmp_projet (secteur_id)');
        $this->addSql('CREATE INDEX idx_522fe58dd30f6f97 ON prmp_projet (engagement_id)');
        $this->addSql('CREATE INDEX idx_522fe58dc54c8c93 ON prmp_projet (type_id)');
        $this->addSql('ALTER TABLE topology.layer ADD CONSTRAINT layer_topology_id_fkey FOREIGN KEY (topology_id) REFERENCES topology (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prmp_projet ADD CONSTRAINT fk_522fe58dd30f6f97 FOREIGN KEY (engagement_id) REFERENCES prm_engagement (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prmp_projet ADD CONSTRAINT fk_522fe58d9f7e4405 FOREIGN KEY (secteur_id) REFERENCES prm_secteur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prmp_projet ADD CONSTRAINT fk_522fe58dc54c8c93 FOREIGN KEY (type_id) REFERENCES prm_type_projet (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
