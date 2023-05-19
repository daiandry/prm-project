<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201116102737 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE topology.topology_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE prm_titulaire_marcher_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE prm_titulaire_marcher (id INT NOT NULL, nom VARCHAR(255) NOT NULL, contact VARCHAR(150) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('DROP TABLE topology.topology');
        $this->addSql('DROP TABLE topology.layer');
        $this->addSql('ALTER TABLE prm_projet ADD pdm_titulaire_du_marche_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE prm_projet ADD rf_montant_global_projet DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE prm_projet ADD rf_budget_consomme DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE prm_projet DROP collectivite_territoriale_descentralisee');
        $this->addSql('ALTER TABLE prm_projet DROP pdm_date_signature_reel');
        $this->addSql('ALTER TABLE prm_projet DROP pdm_titulaire_du_marche');
        $this->addSql('ALTER TABLE prm_projet DROP pdm_date_export_siig');
        $this->addSql('ALTER TABLE prm_projet ALTER inaugurable TYPE BOOLEAN');
        $this->addSql('ALTER TABLE prm_projet ALTER inaugurable DROP DEFAULT');
        $this->addSql('ALTER TABLE prm_projet ALTER en_retard TYPE BOOLEAN');
        $this->addSql('ALTER TABLE prm_projet ALTER en_retard DROP DEFAULT');
        $this->addSql('ALTER TABLE prm_projet ADD CONSTRAINT FK_B43217837015D35D FOREIGN KEY (pdm_titulaire_du_marche_id) REFERENCES prm_titulaire_marcher (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_B43217837015D35D ON prm_projet (pdm_titulaire_du_marche_id)');
        $this->addSql('ALTER TABLE prm_projet_zone DROP CONSTRAINT FK_61004F3A9F2C3FAB');
        $this->addSql('ALTER TABLE prm_projet_zone DROP CONSTRAINT FK_61004F3AC18272');
        $this->addSql('DROP INDEX "primary"');
        $this->addSql('ALTER TABLE prm_projet_zone ADD CONSTRAINT FK_61004F3A9F2C3FAB FOREIGN KEY (zone_id) REFERENCES prm_zone_geo (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prm_projet_zone ADD CONSTRAINT FK_61004F3AC18272 FOREIGN KEY (projet_id) REFERENCES prm_projet (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prm_projet_zone ADD PRIMARY KEY (projet_id, zone_id)');
        $this->addSql('ALTER TABLE prm_taches ADD projet_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE prm_taches ADD categorie_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE prm_taches ADD type_tache_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE prm_taches DROP projet');
        $this->addSql('ALTER TABLE prm_taches ADD CONSTRAINT FK_DFD546B2C18272 FOREIGN KEY (projet_id) REFERENCES prm_projet (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prm_taches ADD CONSTRAINT FK_DFD546B2BCF5E72D FOREIGN KEY (categorie_id) REFERENCES prm_categorie_tache (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prm_taches ADD CONSTRAINT FK_DFD546B21FDC7AC5 FOREIGN KEY (type_tache_id) REFERENCES prm_type_tache (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_DFD546B2C18272 ON prm_taches (projet_id)');
        $this->addSql('CREATE INDEX IDX_DFD546B2BCF5E72D ON prm_taches (categorie_id)');
        $this->addSql('CREATE INDEX IDX_DFD546B21FDC7AC5 ON prm_taches (type_tache_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SCHEMA topology');
        $this->addSql('ALTER TABLE prm_projet DROP CONSTRAINT FK_B43217837015D35D');
        $this->addSql('DROP SEQUENCE prm_titulaire_marcher_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE topology.topology_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE topology.topology (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, srid INT NOT NULL, "precision" DOUBLE PRECISION NOT NULL, hasz BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX topology_name_key ON topology.topology (name)');
        $this->addSql('CREATE TABLE topology.layer (topology_id INT NOT NULL, layer_id INT NOT NULL, schema_name VARCHAR(255) NOT NULL, table_name VARCHAR(255) NOT NULL, feature_column VARCHAR(255) NOT NULL, feature_type INT NOT NULL, level INT DEFAULT 0 NOT NULL, child_id INT DEFAULT NULL, PRIMARY KEY(topology_id, layer_id))');
        $this->addSql('CREATE UNIQUE INDEX layer_schema_name_table_name_feature_column_key ON topology.layer (schema_name, table_name, feature_column)');
        $this->addSql('CREATE INDEX IDX_181D8D68ED697DD5 ON topology.layer (topology_id)');
        $this->addSql('ALTER TABLE topology.layer ADD CONSTRAINT layer_topology_id_fkey FOREIGN KEY (topology_id) REFERENCES topology (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE prm_titulaire_marcher');
        $this->addSql('DROP INDEX IDX_B43217837015D35D');
        $this->addSql('ALTER TABLE prm_projet ADD collectivite_territoriale_descentralisee VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE prm_projet ADD pdm_date_signature_reel TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE prm_projet ADD pdm_titulaire_du_marche VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE prm_projet ADD pdm_date_export_siig TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE prm_projet DROP pdm_titulaire_du_marche_id');
        $this->addSql('ALTER TABLE prm_projet DROP rf_montant_global_projet');
        $this->addSql('ALTER TABLE prm_projet DROP rf_budget_consomme');
        $this->addSql('ALTER TABLE prm_projet ALTER inaugurable TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE prm_projet ALTER inaugurable DROP DEFAULT');
        $this->addSql('ALTER TABLE prm_projet ALTER en_retard TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE prm_projet ALTER en_retard DROP DEFAULT');
        $this->addSql('ALTER TABLE prm_taches DROP CONSTRAINT FK_DFD546B2C18272');
        $this->addSql('ALTER TABLE prm_taches DROP CONSTRAINT FK_DFD546B2BCF5E72D');
        $this->addSql('ALTER TABLE prm_taches DROP CONSTRAINT FK_DFD546B21FDC7AC5');
        $this->addSql('DROP INDEX IDX_DFD546B2C18272');
        $this->addSql('DROP INDEX IDX_DFD546B2BCF5E72D');
        $this->addSql('DROP INDEX IDX_DFD546B21FDC7AC5');
        $this->addSql('ALTER TABLE prm_taches ADD projet INT NOT NULL');
        $this->addSql('ALTER TABLE prm_taches DROP projet_id');
        $this->addSql('ALTER TABLE prm_taches DROP categorie_id');
        $this->addSql('ALTER TABLE prm_taches DROP type_tache_id');
        $this->addSql('ALTER TABLE prm_projet_zone DROP CONSTRAINT fk_61004f3ac18272');
        $this->addSql('ALTER TABLE prm_projet_zone DROP CONSTRAINT fk_61004f3a9f2c3fab');
        $this->addSql('DROP INDEX prm_projet_zone_pkey');
        $this->addSql('ALTER TABLE prm_projet_zone ADD CONSTRAINT fk_61004f3ac18272 FOREIGN KEY (projet_id) REFERENCES prm_zone_geo (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prm_projet_zone ADD CONSTRAINT fk_61004f3a9f2c3fab FOREIGN KEY (zone_id) REFERENCES prm_projet (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prm_projet_zone ADD PRIMARY KEY (zone_id, projet_id)');
    }
}
