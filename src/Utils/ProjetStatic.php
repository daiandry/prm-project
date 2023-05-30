<?php
/**
 * Created by PhpStorm.
 * User: daiandry
 * Date: 09/11/2020
 * Time: 11:06
 */

namespace App\Utils;
/**
 * ackage App\Utils
 */
class ProjetStatic
{
    /**
     * @return string
     */
    public static function formatProjet()
    {
        $criteria = " distinct(p.id) as id,
p.date_create,
p.date_modify,
p.engagement_id as engagement,
pg.libelle as engagement_libelle,
p.secteur_id as secteur,
ps.libelle as secteur_libelle,
p.type_id as type,
pt.libelle as type_libelle,
p.nom,
pzg.libelle as zone_libelle,
p.conv_cl,
p.pdm_date_fin_appel_offre,
p.pdm_date_signature_contrat,
p.pdm_titulaire_du_marche_id as titulaire_du_marche_id,
ptm.nom as titulaire_du_marche_nom,
p.pdm_designation, 
p.pdm_tiers_nif,
p.pdm_date_lancement_os,
p.pdm_date_lancement_travaux_prevu,
p.pdm_date_travaux_reel,
p.pdm_delai_execution_prevu,
p.pdm_date_fin_prevu,
p.rf_autorisation_engagement,
p.rf_autorisation_engagement,
p.avancement,
p.observation,
p.soa_code,
p.pcop_compte,
p.promesse_presidentielle,
p.inaugurable as projet_inaugurable,
p.en_retard,
p.situation_actuelle_travaux,
p.date_inauguration,
p.priorite_id as priorite,
pr.libelle as priorite_libelle,
p.categorie_id as categorie,
pc.libelle as categorie_libelle,
p.pdm_date_debut_appel_offre,
p.rf_date_signature_autorisation_engagement,
p.rf_autorisation_engagement,
p.rf_credit_payement_annee_en_cours,
p.rf_montant_depenses_decaissess_mandate,
p.rf_montant_depenses_decaissess_liquide,
p.rf_exercice_budgetaire,
p.rf_budget_consomme,
p.situation_actuelle_marche_id as situation_projet,
psp.libelle as situation_projet_libelle,
p.description,
p.coordonnegps,
p.valide,
p.projet_id as projet_parent_id,
pp.nom as projet_parent_nom,
p.statut_id as statut,
ptp.libelle as statut_libelle,
ptp.couleur as statut_couleur,
p.created_by_id as created_by,
pu.email as created_by_email ";
        return $criteria;
    }

    /**
     * @return string
     */
    public static function formatListProjet()
    {
        $criteria = " distinct(p.id) as id,
p.nom,
p.date_create,
p.date_modify,
p.soa_code,
p.projet_id,
p.pdm_date_fin_prevu,
p.description,
p.avancement,
p.coordonnegps,
p.statut_id as statut,
ptp.libelle as statut_libelle,
ptp.couleur as statut_couleur,
ptp.progressbar as statut_progressbar,
p.rf_budget_consomme as budget_execute,
p.rf_autorisation_engagement ";
        return $criteria;
    }

    /**
     * @return string
     */
    public function selectIdListProjet(){
        $query="SELECT distinct(p.id) as id,p.projet_id, p.date_create
                FROM public.prm_projet p
                left join prm_engagement pg on p.engagement_id = pg.id
                left join prm_secteur ps on p.secteur_id = ps.id
                left join prm_type_projet pt on p.type_id = pt.id
                left join prm_categorie_projet pc on p.categorie_id = pc.id
                left join prm_priorite_projet pr on p.priorite_id = pr.id
                left join prm_situation_projet psp on p.situation_actuelle_marche_id = psp.id
                left join prm_projet_zone pzp on pzp.projet_id = p.id
                left join prm_zone_geo pzg on pzp.zone_id = pzg.id
                left join prm_statut_projet ptp on p.statut_id = ptp.id
                inner join prm_user pu on p.created_by_id = pu.id
                left join prm_administration pa on pu.administration_id = pa.id
                left join prm_projet pp on p.projet_id = pp.id ";
        return $query;
    }

    /**
     * @return string
     */
    public static function formatMinInfoListProjet()
    {
        $criteria = " distinct(p.id) as id,
p.coordonneGPS,
p.statut_id ";
        return $criteria;
    }

    /**
     * @return string
     */
    public static function formatProjetByIdMinInfo()
    {
        $criteria = " distinct(p.id) as id,
p.nom,
p.description,
ptp.libelle as statut_libelle";
        return $criteria;
    }
}