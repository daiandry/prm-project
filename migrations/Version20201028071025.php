<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201028071025 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE prm_affectation_projet_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE prm_categorie_projet_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE prm_categorie_tache_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE prm_doc_type_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE prm_documents_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE prm_engagement_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE prm_photos_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE prm_priorite_projet_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE prm_projet_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE prm_secteur_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE prm_situation_projet_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE prm_statut_tache_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE prm_taches_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE prm_type_projet_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE prm_type_tache_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE prm_type_zone_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE prm_zone_geo_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE prm_affectation_projet (id INT NOT NULL, user_id INT NOT NULL, projet_id INT NOT NULL, date_validation TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, date_affectation TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, valide VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE prm_categorie_projet (id INT NOT NULL, libelle VARCHAR(150) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE prm_categorie_tache (id INT NOT NULL, libelle VARCHAR(150) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE prm_doc_type (id INT NOT NULL, libelle VARCHAR(150) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE prm_documents (id INT NOT NULL, tache_id INT DEFAULT NULL, type_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, projet VARCHAR(100) NOT NULL, chemin VARCHAR(255) NOT NULL, upload_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, description VARCHAR(255) NOT NULL, statut VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EA1745B1D2235D39 ON prm_documents (tache_id)');
        $this->addSql('CREATE INDEX IDX_EA1745B1C54C8C93 ON prm_documents (type_id)');
        $this->addSql('CREATE TABLE prm_engagement (id INT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE prm_photos (id INT NOT NULL, tache_id INT DEFAULT NULL, projet_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EC516BF3D2235D39 ON prm_photos (tache_id)');
        $this->addSql('CREATE INDEX IDX_EC516BF3C18272 ON prm_photos (projet_id)');
        $this->addSql('CREATE TABLE prm_priorite_projet (id INT NOT NULL, libelle VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE prm_projet (id INT NOT NULL, engagement_id INT DEFAULT NULL, priorite_id INT DEFAULT NULL, categorie_id INT DEFAULT NULL, secteur_id INT DEFAULT NULL, type_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, conv_cl VARCHAR(255) NOT NULL, projet_parent_id INT NOT NULL, collectivite_territoriale_descentralisee VARCHAR(255) NOT NULL, secteur_activite VARCHAR(255) NOT NULL, pdm_date_fin_appel_offre TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, pdm_date_signature_prevu TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, pdm_date_signature_reel TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, pdm_titulaire_du_marche VARCHAR(255) NOT NULL, pdm_designation VARCHAR(255) NOT NULL, pdm_tiers_nif VARCHAR(255) NOT NULL, pdm_notification_et_ordre_service VARCHAR(255) NOT NULL, pdm_date_export_siig TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, pdm_date_lancement_travaux_prevu TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, pdm_date_travaux_reel TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, pdm_delai_execution_prevu TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, pdm_date_fin_prevu TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, rf_autorisation_engagement VARCHAR(255) NOT NULL, situation_actuelle_marche_public_id INT NOT NULL, observation VARCHAR(255) NOT NULL, soa_code VARCHAR(255) NOT NULL, pcop_compte VARCHAR(255) NOT NULL, promesse_presidentielle VARCHAR(255) NOT NULL, inaugurable VARCHAR(255) NOT NULL, en_retard VARCHAR(255) NOT NULL, date_inauguration TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_by INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B4321783D30F6F97 ON prm_projet (engagement_id)');
        $this->addSql('CREATE INDEX IDX_B432178353B4F1DE ON prm_projet (priorite_id)');
        $this->addSql('CREATE INDEX IDX_B4321783BCF5E72D ON prm_projet (categorie_id)');
        $this->addSql('CREATE INDEX IDX_B43217839F7E4405 ON prm_projet (secteur_id)');
        $this->addSql('CREATE INDEX IDX_B4321783C54C8C93 ON prm_projet (type_id)');
        $this->addSql('CREATE TABLE prm_projet_zone (zone_id INT NOT NULL, projet_id INT NOT NULL, PRIMARY KEY(zone_id, projet_id))');
        $this->addSql('CREATE INDEX IDX_61004F3A9F2C3FAB ON prm_projet_zone (zone_id)');
        $this->addSql('CREATE INDEX IDX_61004F3AC18272 ON prm_projet_zone (projet_id)');
        $this->addSql('CREATE TABLE prm_secteur (id INT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE prm_situation_projet (id INT NOT NULL, libelle VARCHAR(150) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE prm_statut_tache (id INT NOT NULL, libelle VARCHAR(150) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE prm_taches (id INT NOT NULL, statut_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, projet_id INT NOT NULL, date_realisation_prevu TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, date_realisation_reel VARCHAR(255) NOT NULL, avancement VARCHAR(255) NOT NULL, observation VARCHAR(255) NOT NULL, valeur_prevu VARCHAR(255) NOT NULL, valeur_reel VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DFD546B2F6203804 ON prm_taches (statut_id)');
        $this->addSql('CREATE TABLE prm_type_projet (id INT NOT NULL, libelle VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE prm_type_tache (id INT NOT NULL, libelle VARCHAR(150) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE prm_type_zone (id INT NOT NULL, libelle VARCHAR(100) NOT NULL, code VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE prm_zone_geo (id INT NOT NULL, type_id INT DEFAULT NULL, libelle VARCHAR(100) NOT NULL, code VARCHAR(100) NOT NULL, geom geometry(GEOMETRY, 0) DEFAULT NULL, left_bound INT DEFAULT NULL, right_bound INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4810ABF7C54C8C93 ON prm_zone_geo (type_id)');
        $this->addSql('ALTER TABLE prm_documents ADD CONSTRAINT FK_EA1745B1D2235D39 FOREIGN KEY (tache_id) REFERENCES prm_taches (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prm_documents ADD CONSTRAINT FK_EA1745B1C54C8C93 FOREIGN KEY (type_id) REFERENCES prm_doc_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prm_photos ADD CONSTRAINT FK_EC516BF3D2235D39 FOREIGN KEY (tache_id) REFERENCES prm_taches (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prm_photos ADD CONSTRAINT FK_EC516BF3C18272 FOREIGN KEY (projet_id) REFERENCES prm_type_projet (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prm_projet ADD CONSTRAINT FK_B4321783D30F6F97 FOREIGN KEY (engagement_id) REFERENCES prm_engagement (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prm_projet ADD CONSTRAINT FK_B432178353B4F1DE FOREIGN KEY (priorite_id) REFERENCES prm_priorite_projet (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prm_projet ADD CONSTRAINT FK_B4321783BCF5E72D FOREIGN KEY (categorie_id) REFERENCES prm_categorie_projet (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prm_projet ADD CONSTRAINT FK_B43217839F7E4405 FOREIGN KEY (secteur_id) REFERENCES prm_secteur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prm_projet ADD CONSTRAINT FK_B4321783C54C8C93 FOREIGN KEY (type_id) REFERENCES prm_type_projet (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prm_projet_zone ADD CONSTRAINT FK_61004F3A9F2C3FAB FOREIGN KEY (zone_id) REFERENCES prm_projet (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prm_projet_zone ADD CONSTRAINT FK_61004F3AC18272 FOREIGN KEY (projet_id) REFERENCES prm_zone_geo (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prm_taches ADD CONSTRAINT FK_DFD546B2F6203804 FOREIGN KEY (statut_id) REFERENCES prm_statut_tache (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prm_zone_geo ADD CONSTRAINT FK_4810ABF7C54C8C93 FOREIGN KEY (type_id) REFERENCES prm_type_zone (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE prm_projet DROP CONSTRAINT FK_B4321783BCF5E72D');
        $this->addSql('ALTER TABLE prm_documents DROP CONSTRAINT FK_EA1745B1C54C8C93');
        $this->addSql('ALTER TABLE prm_projet DROP CONSTRAINT FK_B4321783D30F6F97');
        $this->addSql('ALTER TABLE prm_projet DROP CONSTRAINT FK_B432178353B4F1DE');
        $this->addSql('ALTER TABLE prm_projet_zone DROP CONSTRAINT FK_61004F3A9F2C3FAB');
        $this->addSql('ALTER TABLE prm_projet DROP CONSTRAINT FK_B43217839F7E4405');
        $this->addSql('ALTER TABLE prm_taches DROP CONSTRAINT FK_DFD546B2F6203804');
        $this->addSql('ALTER TABLE prm_documents DROP CONSTRAINT FK_EA1745B1D2235D39');
        $this->addSql('ALTER TABLE prm_photos DROP CONSTRAINT FK_EC516BF3D2235D39');
        $this->addSql('ALTER TABLE prm_photos DROP CONSTRAINT FK_EC516BF3C18272');
        $this->addSql('ALTER TABLE prm_projet DROP CONSTRAINT FK_B4321783C54C8C93');
        $this->addSql('ALTER TABLE prm_zone_geo DROP CONSTRAINT FK_4810ABF7C54C8C93');
        $this->addSql('ALTER TABLE prm_projet_zone DROP CONSTRAINT FK_61004F3AC18272');
        $this->addSql('DROP SEQUENCE prm_affectation_projet_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE prm_categorie_projet_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE prm_categorie_tache_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE prm_doc_type_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE prm_documents_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE prm_engagement_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE prm_photos_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE prm_priorite_projet_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE prm_projet_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE prm_secteur_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE prm_situation_projet_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE prm_statut_tache_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE prm_taches_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE prm_type_projet_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE prm_type_tache_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE prm_type_zone_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE prm_zone_geo_id_seq CASCADE');
        $this->addSql('DROP TABLE prm_affectation_projet');
        $this->addSql('DROP TABLE prm_categorie_projet');
        $this->addSql('DROP TABLE prm_categorie_tache');
        $this->addSql('DROP TABLE prm_doc_type');
        $this->addSql('DROP TABLE prm_documents');
        $this->addSql('DROP TABLE prm_engagement');
        $this->addSql('DROP TABLE prm_photos');
        $this->addSql('DROP TABLE prm_priorite_projet');
        $this->addSql('DROP TABLE prm_projet');
        $this->addSql('DROP TABLE prm_projet_zone');
        $this->addSql('DROP TABLE prm_secteur');
        $this->addSql('DROP TABLE prm_situation_projet');
        $this->addSql('DROP TABLE prm_statut_tache');
        $this->addSql('DROP TABLE prm_taches');
        $this->addSql('DROP TABLE prm_type_projet');
        $this->addSql('DROP TABLE prm_type_tache');
        $this->addSql('DROP TABLE prm_type_zone');
        $this->addSql('DROP TABLE prm_zone_geo');
    }
}
