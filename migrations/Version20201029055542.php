<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201029055542 extends AbstractMigration
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
        $this->addSql('DROP SEQUENCE limite_regions_mada_gid_seq CASCADE');
        $this->addSql('DROP SEQUENCE pop_fkt_gid_seq CASCADE');
        $this->addSql('DROP SEQUENCE topology.topology_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE prm_unite_indicateur_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE prm_unite_monetaire_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE prm_unite_indicateur (id INT NOT NULL, libelle VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE prm_unite_monetaire (id INT NOT NULL, libelle VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('DROP TABLE topology.topology');
        $this->addSql('DROP TABLE topology.layer');
        $this->addSql('DROP TABLE prm_type_secteur');
        $this->addSql('DROP TABLE lim_dist');
        $this->addSql('DROP TABLE lim_com201118');
        $this->addSql('DROP TABLE limite_regions_mada');
        $this->addSql('DROP TABLE pop_fkt');
        $this->addSql('ALTER TABLE prm_type_tache ADD categorie_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE prm_type_tache ADD indicateur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE prm_type_tache ADD monnaie_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE prm_type_tache ADD CONSTRAINT FK_C2C688BDBCF5E72D FOREIGN KEY (categorie_id) REFERENCES prm_type_projet (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prm_type_tache ADD CONSTRAINT FK_C2C688BDDA3B8F3D FOREIGN KEY (indicateur_id) REFERENCES prm_type_projet (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prm_type_tache ADD CONSTRAINT FK_C2C688BD98D3FE22 FOREIGN KEY (monnaie_id) REFERENCES prm_unite_monetaire (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_C2C688BDBCF5E72D ON prm_type_tache (categorie_id)');
        $this->addSql('CREATE INDEX IDX_C2C688BDDA3B8F3D ON prm_type_tache (indicateur_id)');
        $this->addSql('CREATE INDEX IDX_C2C688BD98D3FE22 ON prm_type_tache (monnaie_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SCHEMA topology');
        $this->addSql('ALTER TABLE prm_type_tache DROP CONSTRAINT FK_C2C688BD98D3FE22');
        $this->addSql('DROP SEQUENCE prm_unite_indicateur_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE prm_unite_monetaire_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE lim_dist_gid_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE lim_com201118_gid_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE prmp_projet_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE prm_type_secteur_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE limite_regions_mada_gid_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE pop_fkt_gid_seq INCREMENT BY 1 MINVALUE 1 START 1');
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
        $this->addSql('CREATE TABLE limite_regions_mada (gid SERIAL NOT NULL, codregion VARCHAR(3) DEFAULT NULL, nom_region VARCHAR(40) DEFAULT NULL, coddist VARCHAR(5) DEFAULT NULL, nom_distri VARCHAR(40) DEFAULT NULL, codcom VARCHAR(7) DEFAULT NULL, nom_commun VARCHAR(30) DEFAULT NULL, geom geometry(GEOMETRY, 0) DEFAULT NULL, PRIMARY KEY(gid))');
        $this->addSql('CREATE INDEX limite_regions_mada_geom_idx ON limite_regions_mada (geom)');
        $this->addSql('CREATE TABLE pop_fkt (gid SERIAL NOT NULL, region VARCHAR(254) DEFAULT NULL, district VARCHAR(254) DEFAULT NULL, commune VARCHAR(254) DEFAULT NULL, fokontany VARCHAR(254) DEFAULT NULL, population BIGINT DEFAULT NULL, densite_ha DOUBLE PRECISION DEFAULT NULL, geom geometry(GEOMETRY, 0) DEFAULT NULL, PRIMARY KEY(gid))');
        $this->addSql('CREATE INDEX pop_fkt_geom_idx ON pop_fkt (geom)');
        $this->addSql('ALTER TABLE topology.layer ADD CONSTRAINT layer_topology_id_fkey FOREIGN KEY (topology_id) REFERENCES topology (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE prm_unite_indicateur');
        $this->addSql('DROP TABLE prm_unite_monetaire');
        $this->addSql('ALTER TABLE prm_type_tache DROP CONSTRAINT FK_C2C688BDBCF5E72D');
        $this->addSql('ALTER TABLE prm_type_tache DROP CONSTRAINT FK_C2C688BDDA3B8F3D');
        $this->addSql('DROP INDEX IDX_C2C688BDBCF5E72D');
        $this->addSql('DROP INDEX IDX_C2C688BDDA3B8F3D');
        $this->addSql('DROP INDEX IDX_C2C688BD98D3FE22');
        $this->addSql('ALTER TABLE prm_type_tache DROP categorie_id');
        $this->addSql('ALTER TABLE prm_type_tache DROP indicateur_id');
        $this->addSql('ALTER TABLE prm_type_tache DROP monnaie_id');
    }
}
