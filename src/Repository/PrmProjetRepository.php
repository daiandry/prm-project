<?php

namespace App\Repository;

use App\Entity\PrmProjet;
use App\Entity\PrmStatutProjet;
use App\Entity\PrmTaches;
use App\Entity\PrmTypeTache;
use App\Entity\User;
use App\Utils\ConstantSrv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use App\Utils\ProjetStatic;
use function PHPUnit\Framework\isNull;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @method PrmProjet|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrmProjet|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrmProjet[]    findAll()
 * @method PrmProjet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrmProjetRepository extends ServiceEntityRepository
{

    const A_FAIRE_ID = 1;
    const EN_COURS_ID = 2;
    const TERMINE_ID = 3;
    const EN_RETARD = true;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrmProjet::class);
    }

    /**
     * @return mixed[]
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getListProjetParent()
    {
        $conn = $this->getEntityManager()->getConnection();
        $query = "select id,nom from prm_projet where projet_id is null";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @param $data
     * @param bool $all
     * @param $administration
     * @param $region
     * @return array
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAllProjet($data, $all = false, $administration, $region, User $user = null)
    {
        $page = $data['page'];
        $limit = $data['limit'];

        $whereExist = false;
        $whereOr = false;
        $paginate = false;
        $debut = 1;
        $premier = $page;
        if (!is_null($page) && !empty($page)) {
            $debut = $page;
        }
        if (!is_null($limit) && !empty($limit)) {
            $paginate = true;
        } else {
            if (!is_null($page) && !empty($page)) {
                $limit = 100;
                $paginate = true;
            }
        }
        if ($paginate) {
            $premier = $limit * max(0, $debut - 1);
        }
        $conn = $this->getEntityManager()->getConnection();
        $query = ProjetStatic::selectIdListProjet();
        $idProfil = $user?($user->getProfil()?$user->getProfil()->getId():null):null;
        $idCurrentUser = $user->getId();

        if ($all == false) {

            $query .= " left join prm_affectation_projet pap on ((pu.profil_id = pap.profil_id) or (pap.user_id = pu.id))";
        } else {
            $query .= " left join prm_affectation_projet pap on ((pu.administration_id = pap.administration_id) or (pap.region_id = pu.region_id) or (pap.user_id = pu.id))";
        }
        //-- administration --
        if (($administration != null) || ($region != null)) {
            $queryAdmin = "";
            $queryRegion = "";
            if ($whereExist == false) {
                if ($administration != null) {
                    $queryAdmin = 'pap.administration_id = ' . $administration;
                    $whereOr = true;
                }
                if ($whereOr == true) {
                    $queryRegion = ' or pap.region_id = ' . $region;
                    $queryRegion = ($region != null) ? $queryRegion : '';
                } else {
                    $queryRegion = 'pap.region_id = ' . $region;
                    $queryRegion = ($region != null) ? $queryRegion : '';
                }
                $q = $queryAdmin . $queryRegion;
                $query .= " WHERE (($q OR (pap.is_institution_collect is false or pap.is_institution_collect is null) ) AND (select exists ( select afp.id as nbr from prm_affectation_projet afp where afp.projet_id = p.id and afp.profil_id = $idProfil and afp.user_id = $idCurrentUser ) ))";
                $whereExist = true;
            } else {
                if ($administration != null) {
                    $queryAdmin = 'pap.administration_id = ' . $administration;
                    $whereOr = true;
                }
                if ($whereOr == true) {
                    $queryRegion = ' or pap.region_id = ' . $region;
                    $queryRegion = ($region != null) ? $queryRegion : '';
                } else {
                    $queryRegion = 'pap.region_id = ' . $region;
                    $queryRegion = ($region != null) ? $queryRegion : '';
                }
                $q = $queryAdmin . $queryRegion;
                $query .= " AND (($q OR (pap.is_institution_coll ect is false or pap.is_institution_collect is null)) AND (select exists ( select afp.id as nbr from prm_affectation_projet afp where afp.projet_id = p.id and afp.profil_id = $idProfil and afp.user_id = $idCurrentUser ) ))";
            }
        }
        //-- nom --
        if (($data['nom'] != null) && (trim($data['nom']) != "") && ($data['nom'] != -1)) {
            if ($whereExist == false) {
                $query .= " WHERE (UPPER(p.nom) like UPPER('%" . $data['nom'] . "%'))";
                $whereExist = true;
            } else {
                $query .= " AND (UPPER(p.nom) like UPPER('%" . $data['nom'] . "%'))";
            }
        }
        //-- statut projet --
        if (($data['statut'] != null) && $data['statut'] != -1) {
            if ($whereExist == false) {
                $query .= " WHERE (p.statut_id = " . $data['statut'] . ")";
                $whereExist = true;
            } else {
                $query .= " AND (p.statut_id = " . $data['statut'] . ")";
            }
        }
        //-- conv_cl projet --
        if (($data['conv_cl'] != null) && (trim($data['conv_cl']) != "") && ($data['conv_cl'] != -1)) {
            if ($whereExist == false) {
                $query .= " WHERE (UPPER(p.conv_cl) like UPPER('%" . $data['conv_cl'] . "%'))";
                $whereExist = true;
            } else {
                $query .= " AND (UPPER(p.conv_cl) like UPPER('%" . $data['conv_cl'] . "%'))";
            }
        }
        //-- en retard --
        if (($data['en_retard'] !== null) && ($data['en_retard'] !== -1)) {
            if ($whereExist == false) {
                $query .= " WHERE (p.en_retard = cast(" . $data['en_retard'] . " as bool))";
                $whereExist = true;
            } else {
                $query .= " AND (p.en_retard = cast(" . $data['en_retard'] . " as bool))";
            }
        }
        //-- projet_inaugurable --
        if (($data['projet_inaugurable'] !== null) && ($data['projet_inaugurable'] !== -1)) {
            if ($whereExist == false) {
                $query .= " WHERE (p.inaugurable = cast(" . $data['projet_inaugurable'] . " as bool))";
                $whereExist = true;
            } else {
                $query .= " AND (p.inaugurable = cast(" . $data['projet_inaugurable'] . " as bool))";
            }
        }
        //-- pdm_date_fin_prevu --
        if (($data['pdm_date_fin_prevu_debut'] != null) && ($data['pdm_date_fin_prevu_debut'] != -1) && ($data['pdm_date_fin_prevu_fin'] != null) && ($data['pdm_date_fin_prevu_fin'] != -1)) {
            if ($whereExist == false) {
                $query .= " WHERE cast(p.pdm_date_fin_prevu as date) >= cast('" . $data['pdm_date_fin_prevu_debut'] . "' as date) AND cast(p.pdm_date_fin_prevu as date) <= cast('" . $data['pdm_date_fin_prevu_fin'] . "' as date)";
                $whereExist = true;
            } else {
                $query .= " AND cast(p.pdm_date_fin_prevu as date) >= cast('" . $data['pdm_date_fin_prevu_debut'] . "' as date) AND cast(p.pdm_date_fin_prevu as date) <= cast('" . $data['pdm_date_fin_prevu_fin'] . "' as date)";
            }
        }
        //-- engagement --
        if (($data['engagement'] != null) && $data['engagement'] != -1) {
            if ($whereExist == false) {
                $query .= " WHERE (p.engagement_id = " . $data['engagement'] . ")";
                $whereExist = true;
            } else {
                $query .= " AND (p.engagement_id = " . $data['engagement'] . ")";
            }
        }
        //-- secteur --
        if (($data['secteur'] != null) && $data['secteur'] != -1) {
            if ($whereExist == false) {
                $query .= " WHERE (p.secteur_id = " . $data['secteur'] . ")";
                $whereExist = true;
            } else {
                $query .= " AND (p.secteur_id = " . $data['secteur'] . ")";
            }
        }
        //-- administration --
        if (($data['administration'] != null) && $data['administration'] != -1) {
            if ($whereExist == false) {
                $query .= " WHERE (pu.administration_id = " . $data['administration'] . ")";
                $whereExist = true;
            } else {
                $query .= " AND (pu.administration_id = " . $data['administration'] . ")";
            }
        }
        //-- type_administration --
        if (($data['type_administration'] != null) && $data['type_administration'] != -1) {
            if ($whereExist == false) {
                $query .= " WHERE (pa.type_admin_id = " . $data['type_administration'] . ")";
                $whereExist = true;
            } else {
                $query .= " AND (pa.type_admin_id = " . $data['type_administration'] . ")";
            }
        }
        //-- zone geo --
        if (($data['zone'] != null) && ($data['zone'] != -1) && ($data['type_zone'] != null) && ($data['type_zone'] != -1) && in_array($data['type_zone'], [1, 2, 3, 4, 5])) {
            if ($whereExist == false) {
                $query .= " where pzg.left_bound >= (select left_bound from prm_zone_geo where id = " . $data['zone'] . ")
   AND pzg.right_bound <= (select right_bound from prm_zone_geo where id = " . $data['zone'] . ")";
                $whereExist = true;
            } else {
                $query .= " AND pzg.left_bound >= (select left_bound from prm_zone_geo where id = " . $data['zone'] . ")
   AND pzg.right_bound <= (select right_bound from prm_zone_geo where id = " . $data['zone'] . ")";
            }
        }
        //-- created by --
        if ($all == false) {
            if ($whereExist == false) {
                $query .= " WHERE (pu.profil_id = pap.profil_id)";
                $whereExist = true;
            } else {
                $query .= " AND ((pu.profil_id = pap.profil_id)";
            }
        }
        //-- projet parent --//
        if ($data['projet_parent']) {
            if ($whereExist) {
                $query .= " OR (p.projet_id is null)";
                if (!$all) {
                    $query .= ')';
                }
            } else {
                $query .= " WHERE (p.projet_id is null)";
                $whereExist = true;
            }
        }
        $query .= ' ORDER BY p.date_create DESC';
        $allResult = $query;
        $query .= ' LIMIT ' . $limit . ' OFFSET ' . $premier . ';'; //\dump($query);die;
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $queryResult = $stmt->fetchAll();
        $stmt1 = $conn->prepare($allResult);
        $stmt1->execute();
        $total = $stmt1->rowCount();
        $response = array(
            'total' => $total,
            'list' => $queryResult
        );
        return $response;
    }


    /**
     * @param $data
     * @param bool $all
     * @param $administration
     * @param $region
     * @return array
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAllIdProjectUser($idUser, $all = false, $administration, $region)
    {
        $whereExist = false;
        $whereOr = false;
        $conn = $this->getEntityManager()->getConnection();
        $query = ProjetStatic::selectIdListProjet();
        if ($all == false) {
            $query .= " left join prm_affectation_projet pap on ((pu.profil_id = pap.profil_id) or (pap.user_id = pu.id))";
        } else {
            $query .= " left join prm_affectation_projet pap on ((pu.administration_id = pap.administration_id) or (pap.region_id = pu.region_id) or (pap.user_id = pu.id))";
        }
        //-- administration --
        if (($administration != null) || ($region != null)) {
            $queryAdmin = "";
            $queryRegion = "";
            if ($whereExist == false) {
                if ($administration != null) {
                    $queryAdmin = 'pu.administration_id = ' . $administration;
                    $whereOr = true;
                }
                if ($whereOr == true) {
                    $queryRegion = ' or pu.region_id = ' . $region;
                    $queryRegion = ($region != null) ? $queryRegion : '';
                } else {
                    $queryRegion = 'pu.region_id = ' . $region;
                    $queryRegion = ($region != null) ? $queryRegion : '';
                }
                $q = $queryAdmin . $queryRegion;
                $query .= " WHERE ($q)";
                $whereExist = true;
            } else {
                if ($administration != null) {
                    $queryAdmin = 'pu.administration_id = ' . $administration;
                    $whereOr = true;
                }
                if ($whereOr == true) {
                    $queryRegion = ' or pu.region_id = ' . $region;
                    $queryRegion = ($region != null) ? $queryRegion : '';
                } else {
                    $queryRegion = 'pu.region_id = ' . $region;
                    $queryRegion = ($region != null) ? $queryRegion : '';
                }
                $q = $queryAdmin . $queryRegion;
                $query .= " AND ($q)";
            }
        }
        //-- created by --
        if ($all == false) {
            if ($whereExist == false) {
                $query .= " WHERE (p.created_by_id = " . $idUser . ")";
                $whereExist = true;
            } else {
                $query .= " OR (p.created_by_id = " . $idUser . ")";
            }
        }
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $queryResult = $stmt->fetchAll();
        return $queryResult;
    }

    /**
     * @param $administration
     * @param $region
     * @return mixed[]
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAllProjetInDashboard($administration, $region)
    {
        $whereExist = false;
        $conn = $this->getEntityManager()->getConnection();
        $query = "SELECT distinct(p.id) as id,p.date_create
 FROM public.prm_projet p
inner join prm_statut_projet ptp on p.statut_id = ptp.id
inner join prm_user pu on p.created_by_id = pu.id 
inner join prm_administration pa on pu.administration_id = pa.id
inner join prm_affectation_projet pap on (p.id = pap.projet_id and pap.profil_id = pu.profil_id)
";

        //-- administration --
        if (($administration != null) || ($region != null)) {
            $queryAdmin = "";
            $queryRegion = "";
            if ($whereExist == false) {
                if ($administration != null) {
                    $queryAdmin = 'pu.administration_id = ' . $administration;
                    $whereOr = true;
                }
                if ($whereOr == true) {
                    $queryRegion = ' or pu.region_id = ' . $region;
                    $queryRegion = ($region != null) ? $queryRegion : '';
                } else {
                    $queryRegion = 'pu.region_id = ' . $region;
                    $queryRegion = ($region != null) ? $queryRegion : '';
                }
                $q = $queryAdmin . $queryRegion;
//                $query .= " WHERE ($q)";
                $whereExist = true;
            } else {
                if ($administration != null) {
                    $queryAdmin = 'pu.administration_id = ' . $administration;
                    $whereOr = true;
                }
                if ($whereOr == true) {
                    $queryRegion = ' or pu.region_id = ' . $region;
                    $queryRegion = ($region != null) ? $queryRegion : '';
                } else {
                    $queryRegion = 'pu.region_id = ' . $region;
                    $queryRegion = ($region != null) ? $queryRegion : '';
                }
                $q = $queryAdmin . $queryRegion;
//                $query .= " AND ($q)";
            }
        }
        if ($whereExist == false) {
            $query .= " WHERE (p.valide = true) and (p.projet_id is null)";
            $whereExist = true;
        } else {
            $query .= " AND (p.valide = true) and (p.projet_id is null)";
        }
        $query .= ' order by p.date_create desc limit 10 offset 0;';
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $queryResult = $stmt->fetchAll();
        return $queryResult;
    }

    /**
     * @param $idProjet
     * @return mixed
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getProjetOneByOne($idProjet)
    {
        $conn = $this->getEntityManager()->getConnection();
        $selectedField = ProjetStatic::formatListProjet();
        $query = "SELECT " . $selectedField . "
 FROM public.prm_projet p
left join prm_engagement pg on p.engagement_id = pg.id
left join prm_secteur ps on p.secteur_id = ps.id
left join prm_type_projet pt on p.type_id = pt.id
left join prm_categorie_projet pc on p.categorie_id = pc.id
left join prm_priorite_projet pr on p.priorite_id = pr.id
left join prm_situation_projet psp on p.situation_actuelle_marche_id = psp.id
left join prm_statut_projet ptp on p.statut_id = ptp.id
inner join prm_user pu on p.created_by_id = pu.id
left join prm_administration pa on pu.administration_id = pa.id
left join prm_projet pp on p.projet_id = pp.id 
where p.id = $idProjet";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * @param $idProjet
     * @return mixed[]
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getProjetById($idProjet)
    {
        $conn = $this->getEntityManager()->getConnection();
        $query = "SELECT " . ProjetStatic::formatProjet() . "
 FROM public.prm_projet p
left join prm_engagement pg on p.engagement_id = pg.id
left join prm_secteur ps on p.secteur_id = ps.id
left join prm_type_projet pt on p.type_id = pt.id
left join prm_categorie_projet pc on p.categorie_id = pc.id 
left join prm_priorite_projet pr on p.priorite_id = pr.id
left join prm_situation_projet psp on p.situation_actuelle_marche_id = psp.id
left join prm_titulaire_marcher ptm on p.pdm_titulaire_du_marche_id = ptm.id
left join prm_projet_zone pzp on pzp.projet_id = p.id
left join prm_zone_geo pzg on pzp.zone_id = pzg.id
left join prm_statut_projet ptp on p.statut_id = ptp.id
inner join prm_user pu on p.created_by_id = pu.id 
left join prm_projet pp on p.projet_id = pp.id
left join prm_administration pa on pu.administration_id = pa.id where p.id = $idProjet;";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * @param $idProjet
     * @return mixed
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getProjetByIdMinInfo($idProjet)
    {
        $conn = $this->getEntityManager()->getConnection();
        $query = "SELECT " . ProjetStatic::formatProjetByIdMinInfo() . "
 FROM public.prm_projet p
inner join prm_statut_projet ptp on p.statut_id = ptp.id 
where p.id = $idProjet;";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * @param $idZone
     * @return mixed[]
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getProjetInNode($idZone)
    {
        $conn = $this->getEntityManager()->getConnection();
        $query = "SELECT p.id
 FROM public.prm_projet p
inner join prm_projet_zone ppz on ppz.projet_id = p.id
inner join prm_zone_geo pzg on pzg.id = ppz.zone_id
where pzg.right_bound - pzg.left_bound = 1
   AND pzg.left_bound > (select left_bound prm_zone_geo where id = $idZone)
   AND pzg.right_bound < (select right_bound prm_zone_geo where id = $idZone) and pzg.id = $idZone";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @param $idProjet
     * @return mixed[]
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getZoneByProjet($idProjet, $georef = false)
    {
        $conn = $this->getEntityManager()->getConnection();
        $query = "SELECT pzg.id,pzg.libelle,pzg.type_id";
        if ($georef) {
            $query .= ",pzg.geo_ref ";
        }
        $query .= " FROM public.prm_projet p
inner join prm_projet_zone ppz on ppz.projet_id = p.id
inner join prm_zone_geo pzg on pzg.id = ppz.zone_id
inner join prm_type_zone ptz on ptz.id = pzg.type_id
where ppz.projet_id = $idProjet";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @param $idProjet
     * @return mixed[]
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getSousProjet($idProjet)
    {
        $conn = $this->getEntityManager()->getConnection();
        $query = "SELECT p.id
 FROM public.prm_projet p
where p.projet_id = $idProjet";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @param $idProjet
     * @param $statut
     * @param $idUser
     * @return mixed[]
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getProfilAffecter($idProjet, $statut, $userAdministration, $userRegion)
    {
        $whereOr = false;
        $conn = $this->getEntityManager()->getConnection();
        $query = "select distinct(pa.profil_id) as id from prm_affectation_projet pa
inner join prm_projet p on p.id = pa.projet_id
inner join prm_user pu on ((pa.administration_id = pu.administration_id)or(pa.region_id = pu.region_id))
where niveau in (select max(niveau) from prm_affectation_projet where projet_id = $idProjet)
and pa.projet_id = $idProjet
and p.statut_id != $statut
and pa.valide = false ";
        if (($userAdministration != null) || ($userRegion != null)) {
            $queryAdmin = "";
            $queryRegion = "";
            if ($userAdministration != null) {
                $queryAdmin = 'pu.administration_id = ' . $userAdministration;
                $whereOr = true;
            }
            if ($whereOr == true) {
                $queryRegion = ' or pu.region_id = ' . $userRegion;
                $queryRegion = ($userRegion != null) ? $queryRegion : '';
            } else {
                $queryRegion = 'pu.region_id = ' . $userRegion;
                $queryRegion = ($userRegion != null) ? $queryRegion : '';
            }
            $q = $queryAdmin . $queryRegion;
            $query .= " and ($q)";
        }
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @return mixed[]
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAllProjetParent($administration, $region)
    {
        $whereExist = true;
        $conn = $this->getEntityManager()->getConnection();
        $query = "select p.id, p.nom from prm_projet p 
INNER JOIN prm_user pu on p.created_by_id = pu.id
where p.projet_id is null";
        //-- administration --
        if (($administration != null) || ($region != null)) {
            $queryAdmin = "";
            $queryRegion = "";
            if ($whereExist == false) {
                if ($administration != null) {
                    $queryAdmin = 'pu.administration_id = ' . $administration;
                    $whereOr = true;
                }
                if ($whereOr == true) {
                    $queryRegion = ' or pu.region_id = ' . $region;
                    $queryRegion = ($region != null) ? $queryRegion : '';
                } else {
                    $queryRegion = 'pu.region_id = ' . $region;
                    $queryRegion = ($region != null) ? $queryRegion : '';
                }
                $q = $queryAdmin . $queryRegion;
                $query .= " WHERE ($q)";
                $whereExist = true;
            } else {
                if ($administration != null) {
                    $queryAdmin = 'pu.administration_id = ' . $administration;
                    $whereOr = true;
                }
                if ($whereOr == true) {
                    $queryRegion = ' or pu.region_id = ' . $region;
                    $queryRegion = ($region != null) ? $queryRegion : '';
                } else {
                    $queryRegion = 'pu.region_id = ' . $region;
                    $queryRegion = ($region != null) ? $queryRegion : '';
                }
                $q = $queryAdmin . $queryRegion;
                $query .= " AND ($q)";
            }
        }
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @param $idZone
     * @return mixed[]
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getProjetInLeaf($idZone)
    {
        $conn = $this->getEntityManager()->getConnection();
        $query = "SELECT p.id
FROM public .prm_projet p
inner join prm_projet_zone ppz on ppz.projet_id = p.id
inner join prm_zone_geo pzg on pzg.id = ppz.zone_id
where ppz.zone_id = $idZone";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @param bool $total
     * @param bool $restant
     * @param bool $afaire
     * @param bool $realised
     * @return mixed
     */
    public function getTotalProjet($total = false, $restantAfaire = false, $enCours = false, $realised = false, $retard = false, $surcout = false, $ordre_service = false, $bodyQuery, ContainerInterface $container)
    {
        $query = $this->createQueryBuilder('pp');
        $query->select("COUNT(pp.id) as total");

        if ($restantAfaire && !is_null($restantAfaire)) {
            $query
                ->andWhere($query->expr()->eq('pp.statut', ':idAfaire'))
                ->setParameter('idAfaire', ConstantSrv::STATUT_PROJET_A_FAIRE);
        }
        if ($enCours && !is_null($enCours)) {
            $query
                ->andWhere($query->expr()->eq('pp.statut', ':idEnCours'))
                ->setParameter('idEnCours', ConstantSrv::STATUT_PROJET_EN_COURS);
        }

        if ($realised && !is_null($realised)) {
            $query
                ->andWhere($query->expr()->eq('pp.statut', ':idRealised'))
                ->setParameter('idRealised', ConstantSrv::STATUT_PROJET_TERMINE);
        }

        if ($retard && !is_null($retard)) {
            $query
                ->andWhere($query->expr()->eq('pp.en_retard', ':retard'))
                ->setParameter('retard', self::EN_RETARD);
        }

        if ($surcout && !is_null($surcout)) {
            $query
                ->leftJoin(PrmTaches::class, 'pt', 'WITH', 'pt.projet = pp.id')
                ->leftJoin(PrmTypeTache::class, 'tt', 'WITH', 'pt.typeTache = tt.id')
                ->andWhere($query->expr()->in('tt.id', array(4, 5)));
        }
        if ($ordre_service && !is_null($ordre_service)) {
            $query
                ->andWhere('pp.pdm_date_lancement_os is null');
        }
        $query = $this->kpiFilter($query, $bodyQuery);
        return $query->getQuery()->getResult();
    }

    /**
     * @param $bodyQuery
     * @return mixed
     */
    public function getMinInfoProjet($bodyQuery)
    {
        $query = $this->createQueryBuilder('pp');
        $query->select(ProjetStatic::formatMinInfoListProjet())
            ->innerJoin(PrmStatutProjet::class, 'psp', 'WITH', 'pp.statut = psp.id');
        $query = $this->kpiFilter($query, $bodyQuery);
        //dump($query->getDql());die;
        return $query->getQuery()->getResult();
    }

    /**
     * @param bool $montant_engage_decaisse
     * @param $bodyQuery
     * @return int
     */
    public function returnMontantEngage($montant_engage_decaisse = false, $bodyQuery)
    {
        if ($montant_engage_decaisse == true) {
            $query = $this->createQueryBuilder('pp');
            $query->select("SUM(cast(pt.valeurReel as float)) as somme")
                ->leftJoin(PrmTaches::class, 'pt', 'WITH', 'pt.projet = pp.id')
                ->leftJoin(PrmTypeTache::class, 'tt', 'WITH', 'pt.typeTache = tt.id')
                ->where($query->expr()->eq('tt.id', 2));
            $query = $this->kpiFilter($query, $bodyQuery);
            return $query->getQuery()->getResult();
        } else {
            return 0;
        }
    }

    /**
     * @param bool $montant_engage_decaisse
     * @param $bodyQuery
     * @return int
     */
    public function returnMontantDecaisser($montant_engage_decaisse = false, $bodyQuery)
    {
        if ($montant_engage_decaisse == true) {
            $query = $this->createQueryBuilder('pp');
            $query->select("SUM(cast(pt.valeurReel as float)) as somme")
                ->leftJoin(PrmTaches::class, 'pt', 'WITH', 'pt.projet = pp.id')
                ->leftJoin(PrmTypeTache::class, 'tt', 'WITH', 'pt.typeTache = tt.id')
                ->where($query->expr()->in('tt.id', array(4, 5)));
            $query = $this->kpiFilter($query, $bodyQuery);
            return $query->getQuery()->getResult();
        } else {
            return 0;
        }
    }

    /**
     * @param bool $montant_engage_decaisse
     * @param $bodyQuery
     * @return int
     */
    public function countProjetMontant($montant_engage_decaisse = false, $bodyQuery)
    {
        if ($montant_engage_decaisse == true) {
            $query = $this->createQueryBuilder('pp');
            $query->select("COUNT(pp.id) as totalMontant")
                ->leftJoin(PrmTaches::class, 'pt', 'WITH', 'pt.projet = pp.id')
                ->leftJoin(PrmTypeTache::class, 'tt', 'WITH', 'pt.typeTache = tt.id')
                ->where($query->expr()->in('tt.id', array(2, 4, 5)));
            $query = $this->kpiFilter($query, $bodyQuery);
            return $query->getQuery()->getOneOrNullResult();
        } else {
            return 0;
        }
    }


    /**
     * @param QueryBuilder $query
     * @param $bodyQuery
     * @return QueryBuilder
     */
    public function kpiFilter(QueryBuilder $query, $bodyQuery)
    {
        if (isset($bodyQuery->nomProjet) && !is_null($bodyQuery->nomProjet) && !empty($bodyQuery->nomProjet)) {
            $query->andWhere($query->expr()->like('pp.nom', ':nom'))->setParameter('nom', "%$bodyQuery->nomProjet%");
        }
        if (isset($bodyQuery->typeAdmin) && !is_null($bodyQuery->typeAdmin) && !empty($bodyQuery->inaugurable)) {
            $query
                ->innerJoin('pp.created_by', 'u')
                ->innerJoin('u.administration', 'adm')
                ->innerJoin('adm.typeAdmin', 'tadm')
                ->andWhere($query->expr()->eq('tadm.id', ':tadm'))->setParameter('tadm', "$bodyQuery->typeAdmin");
        }

        if (isset($bodyQuery->statut) && !is_null($bodyQuery->statut) && !empty($bodyQuery->statut)) {
            $query
                ->leftJoin('pp.statut', 'st')
                ->andWhere($query->expr()->eq('st.id', ':statut'))->setParameter('statut', "$bodyQuery->statut");
        }

        if (isset($bodyQuery->dateFinPrev) && !is_null($bodyQuery->dateFinPrev) && !empty($bodyQuery->dateFinPrev)) {
            $query
                ->andWhere($query->expr()->lte('pp.pdm_date_fin_prevu', ':dateFinPrev'))->setParameter('dateFinPrev', "$bodyQuery->dateFinPrev");
        }


        if (isset($bodyQuery->secteur) && !is_null($bodyQuery->secteur) && !empty($bodyQuery->secteur)) {
            $query
                ->leftJoin('pp.secteur', 'secteur')
                ->andWhere($query->expr()->eq('secteur.id', ':secteur'))->setParameter('secteur', "$bodyQuery->secteur");
        }

        if (isset($bodyQuery->engagement) && !is_null($bodyQuery->engagement) && !empty($bodyQuery->engagement)) {
            $query
                ->leftJoin('pp.engagement', 'eng')
                ->andWhere($query->expr()->eq('eng.id', ':engagement'))->setParameter('engagement', "$bodyQuery->engagement");
        }

        if (isset($bodyQuery->region) && !is_null($bodyQuery->region) && !empty($bodyQuery->region)) {
            $query
                ->leftJoin('pp.zone', 'zone')
                ->andWhere($query->expr()->eq('zone.id', ':region'))->setParameter('region', "$bodyQuery->region");
        }

        if (isset($bodyQuery->enRetard) && !is_null($bodyQuery->enRetard) && !empty($bodyQuery->enRetard)) {
            $query
                ->andWhere($query->expr()->eq('pp.en_retard', ':enRetard'))->setParameter('enRetard', "$bodyQuery->enRetard");
        }

        if (isset($bodyQuery->inaugurable) && !is_null($bodyQuery->inaugurable) && !empty($bodyQuery->inaugurable)) {
            $query
                ->andWhere($query->expr()->eq('pp.inaugurable', ':inaugurable'))->setParameter('inaugurable', "$bodyQuery->inaugurable");
        }
        $query->andWhere('pp.projet is null');
        $query->andWhere('pp.valide = true');
        return $query;
    }

    /**
     * @param $idProjet
     * @param $idStatut
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function updateStatutProjet($idProjet, $idStatut)
    {
        $sql = "update prm_projet set statut_id = $idStatut where id = $idProjet";
        $conn = $this->_em->getConnection();
        $qr = $conn->prepare($sql);
        $qr->execute();
    }

    /**
     * ------------------------------------------ KPIV2.0 -----------------------------------------------
     */

    /**
     * @param $data
     * @param $query
     * @return string
     */
    public function queryZone($data, $query)
    {
        if (($data['region'] != null) && ($data['region'] != -1)) {
            $query .= " left join prm_projet_zone pzp on pzp.projet_id = p.id
left join prm_zone_geo pzg on pzp.zone_id = pzg.id";
        }
        return $query;
    }

    /**
     * @param $bodyQuery
     * @return mixed
     */
    public function getMinInfoProjetV2($data, $administration, $region)
    {
        if (($administration != null) && ($region != null)) {
            return [];
        } else {
            $conn = $this->getEntityManager()->getConnection();
            $query = "SELECT " . ProjetStatic::formatMinInfoListProjet() . " FROM public.prm_projet p
inner join prm_user pu on p.created_by_id = pu.id 
inner join prm_administration pa on pu.administration_id = pa.id
inner join prm_affectation_projet pap on (p.id = pap.projet_id and pap.profil_id = pu.profil_id)";
            $query = $this->queryZone($data, $query);
            $query = $this->kpiFilterV2($query, false, $data, $administration, $region);
            if ($data['user']->getAdministration()->getTypeAdmin()->getId() == 1) {
//                $query .= ' AND pu.administration_id = ' . $data['user']->getAdministration()->getId();
            }
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $queryResult = $stmt->fetchAll();
            return $queryResult;
        }
    }

    /**
     * @param $data
     * @param $administration
     * @param $region
     * @return array|mixed[]
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getNombreTotal($data, $administration, $region)
    {
        if (($administration != null) && ($region != null)) {
            return ['total' => 0];
        } else {
            $conn = $this->getEntityManager()->getConnection();
            $query = "SELECT COUNT(distinct(p.id)) as total from public.prm_projet p
inner join prm_user pu on p.created_by_id = pu.id 
inner join prm_administration pa on pu.administration_id = pa.id
inner join prm_affectation_projet pap on (p.id = pap.projet_id and pap.profil_id = pu.profil_id)
";
            $query = $this->queryZone($data, $query);
            $lastQuery = "";
            $query = $this->kpiFilterV2($query, false, $data, $administration, $region, $lastQuery);
            if ($data['user']->getAdministration()->getTypeAdmin()->getId() == 1) {
//                $query .= ' AND pu.administration_id = ' . $data['user']->getAdministration()->getId();
            }
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $queryResult = $stmt->fetchAll();
            return $queryResult;
        }
    }

    /**
     * @param $data
     * @param $administration
     * @param $region
     * @return array|mixed[]
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getNombreRestant($data, $administration, $region)
    {
        if (($administration != null) && ($region != null)) {
            return ['total' => 0];
        } else {
            $whereExist = true;
            $conn = $this->getEntityManager()->getConnection();
            $query = "SELECT COUNT(distinct(p.id)) as total from public.prm_projet p
inner join prm_user pu on p.created_by_id = pu.id 
inner join prm_administration pa on pu.administration_id = pa.id
inner join prm_affectation_projet pap on (p.id = pap.projet_id and pap.profil_id = pu.profil_id)
";
            $query = $this->queryZone($data, $query);
            $query .= " WHERE p.statut_id = " . ConstantSrv::STATUT_PROJET_A_FAIRE . "";
            $lastQuery = "";
            $query = $this->kpiFilterV2($query, $whereExist, $data, $administration, $region, $lastQuery);
            if ($data['user']->getAdministration()->getTypeAdmin()->getId() == 1) {
//                $query .= ' AND pu.administration_id = ' . $data['user']->getAdministration()->getId();
            }
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $queryResult = $stmt->fetchAll();
            return $queryResult;
        }
    }


    /**
     * @param $data
     * @param $administration
     * @param $region
     * @return array|mixed[]
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getNombreEncours($data, $administration, $region)
    {
        if (($administration != null) && ($region != null)) {
            return ['total' => 0];
        } else {
            $whereExist = true;
            $conn = $this->getEntityManager()->getConnection();
            $query = "SELECT COUNT(distinct(p.id)) as total from public.prm_projet p
inner join prm_user pu on p.created_by_id = pu.id 
inner join prm_administration pa on pu.administration_id = pa.id
inner join prm_affectation_projet pap on (p.id = pap.projet_id and pap.profil_id = pu.profil_id)
";
            $query = $this->queryZone($data, $query);
            $query .= " WHERE p.statut_id = " . ConstantSrv::STATUT_PROJET_EN_COURS . "";
            $lastQuery = "";
            $query = $this->kpiFilterV2($query, $whereExist, $data, $administration, $region, $lastQuery);
            if ($data['user']->getAdministration()->getTypeAdmin()->getId() == 1) {
//                $query .= ' AND pu.administration_id = ' . $data['user']->getAdministration()->getId();
            }
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $queryResult = $stmt->fetchAll();
            return $queryResult;
        }
    }


    /**
     * @param $data
     * @param $administration
     * @param $region
     * @return array|mixed[]
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getNombreTerminer($data, $administration, $region)
    {
        if (($administration != null) && ($region != null)) {
            return ['total' => 0];
        } else {
            $whereExist = true;
            $conn = $this->getEntityManager()->getConnection();
            $query = "SELECT COUNT(distinct(p.id)) as total from public.prm_projet p
inner join prm_user pu on p.created_by_id = pu.id
inner join prm_administration pa on pu.administration_id = pa.id
inner join prm_affectation_projet pap on (p.id = pap.projet_id and pap.profil_id = pu.profil_id)
 ";
            $query = $this->queryZone($data, $query);
            $query .= " WHERE p.statut_id = " . ConstantSrv::STATUT_PROJET_TERMINE . "";
            $lastQuery = "";
            $query = $this->kpiFilterV2($query, $whereExist, $data, $administration, $region, $lastQuery);
            if ($data['user']->getAdministration()->getTypeAdmin()->getId() == 1) {
//                $query .= ' AND pu.administration_id = ' . $data['user']->getAdministration()->getId();
            }
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $queryResult = $stmt->fetchAll();
            return $queryResult;
        }
    }

    /**
     * @param $data
     * @param $administration
     * @param $region
     * @return array|mixed[]
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getNombreRetard($data, $administration, $region)
    {
        if (($administration != null) && ($region != null)) {
            return ['total' => 0];
        } else {
            $whereExist = true;
            $conn = $this->getEntityManager()->getConnection();
            $query = "SELECT COUNT(distinct(p.id)) as total from public.prm_projet p
inner join prm_user pu on p.created_by_id = pu.id 
inner join prm_administration pa on pu.administration_id = pa.id
inner join prm_affectation_projet pap on (p.id = pap.projet_id and pap.profil_id = pu.profil_id)
";
            $query = $this->queryZone($data, $query);
            $query .= " WHERE p.en_retard = true";
            $lastQuery = "";
            $query = $this->kpiFilterV2($query, $whereExist, $data, $administration, $region, $lastQuery);
            if ($data['user']->getAdministration()->getTypeAdmin()->getId() == 1) {
//                $query .= ' AND pu.administration_id = ' . $data['user']->getAdministration()->getId();
            }
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $queryResult = $stmt->fetchAll();
            return $queryResult;
        }
    }

    /**
     * @param $data
     * @param $administration
     * @param $region
     * @return array|mixed[]
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getNombreSurcout($data, $administration, $region)
    {
        if (($administration != null) && ($region != null)) {
            return ['total' => 0];
        } else {
            $whereExist = true;
            $conn = $this->getEntityManager()->getConnection();
            $query = "SELECT COUNT(distinct(p.id)) as total 
 FROM prm_projet AS p
  inner join prm_user pu on p.created_by_id = pu.id
  INNER JOIN  (SELECT p.id as proj,p.avancement, SUM(cast(pt.valeur_reel as float)) as somme
FROM prm_taches pt
INNER JOIN prm_projet p on p.id = pt.projet_id
INNER JOIN prm_type_tache tp  on tp.id = pt.type_tache_id
WHERE tp.id IN (4, 5) GROUP BY p.id) sb_request ON sb_request.proj = p.id
inner join prm_administration pa on pu.administration_id = pa.id
inner join prm_affectation_projet pap on (p.id = pap.projet_id and pap.profil_id = pu.profil_id)";
            $query = $this->queryZone($data, $query);
            $query .= " where sb_request.avancement < sb_request.somme ";
            // $lastQuery = " group by p.id";
            $lastQuery = "";
            $query = $this->kpiFilterV2($query, $whereExist, $data, $administration, $region, $lastQuery);
            if ($data['user']->getAdministration()->getTypeAdmin()->getId() == 1) {
//                $query .= ' AND pu.administration_id = ' . $data['user']->getAdministration()->getId();
            }
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $queryResult = $stmt->fetchAll();
            // \dump($queryResult);die;
            return $queryResult;
        }
    }

    /**
     * @param bool $montant_engage_decaisse
     * @param $data
     * @param $administration
     * @param $region
     * @return int|mixed[]
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function returnMontantEngageV2($montant_engage_decaisse = false, $data, $administration, $region)
    {
        if ($montant_engage_decaisse == true) {
            $whereExist = true;
            $conn = $this->getEntityManager()->getConnection();
            $query = "select SUM(cast(pt.valeur_reel as float)) as somme
            from prm_taches pt
            inner join prm_projet p on pt.projet_id = p.id
            inner join prm_type_tache tt on pt.type_tache_id = tt.id 
            inner join prm_user pu on p.created_by_id = pu.id
            inner join prm_administration pa on pu.administration_id = pa.id
            inner join prm_affectation_projet pap on (p.id = pap.projet_id and pap.profil_id = pu.profil_id)";
            $query = $this->queryZone($data, $query);
            $query .= " where tt.id = 2 ";
            $lastQuery = "";
            // $lastQuery = " group by p.id";
            $query = $this->kpiFilterV2($query, $whereExist, $data, $administration, $region, $lastQuery);
            if ($data['user']->getAdministration()->getTypeAdmin()->getId() == 1) {
//                $query .= ' AND pu.administration_id = ' . $data['user']->getAdministration()->getId();
            }
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $queryResult = $stmt->fetchAll();
            return $queryResult;
        } else {
            return 0;
        }
    }

    /**
     * @param bool $montant_engage_decaisse
     * @param $data
     * @param $administration
     * @param $region
     * @return int|mixed[]
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function returnMontantDecaisserV2($montant_engage_decaisse = false, $data, $administration, $region)
    {
        if ($montant_engage_decaisse == true) {
            $whereExist = true;
            $conn = $this->getEntityManager()->getConnection();
            $query = "select SUM(cast(pt.valeur_reel as float)) as somme
            from prm_taches pt
            inner join prm_projet p on pt.projet_id = p.id
            inner join prm_type_tache tt on pt.type_tache_id = tt.id 
            inner join prm_user pu on p.created_by_id = pu.id 
            inner join prm_administration pa on pu.administration_id = pa.id
            inner join prm_affectation_projet pap on (p.id = pap.projet_id and pap.profil_id = pu.profil_id)";
            $query = $this->queryZone($data, $query);
            $query .= " where tt.id in (4,5)";
            // $lastQuery = " group by p.id";
            $lastQuery = "";
            $query = $this->kpiFilterV2($query, $whereExist, $data, $administration, $region, $lastQuery);
            if ($data['user']->getAdministration()->getTypeAdmin()->getId() == 1) {
//                $query .= ' AND pu.administration_id = ' . $data['user']->getAdministration()->getId();
            }
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $queryResult = $stmt->fetchAll();
            return $queryResult;
        } else {
            return 0;
        }
    }

    /**
     * @param bool $montant_engage_decaisse
     * @param $bodyQuery
     * @return int
     */
    public function countProjetMontantV2($montant_engage_decaisse = false, $data, $administration, $region)
    {
        if ($montant_engage_decaisse == true) {
            $whereExist = true;
            $conn = $this->getEntityManager()->getConnection();
            $query = "select COUNT(distinct(p.id)) as total
            from prm_taches pt
            inner join prm_projet p on pt.projet_id = p.id
            inner join prm_type_tache tt on pt.type_tache_id = tt.id ";
            $query = $this->queryZone($data, $query);
            $query .= " where tt.id in (2,4,5)";
            $lastQuery = " group by p.id";
            $query = $this->kpiFilterV2($query, $whereExist, $data, $administration, $region, $lastQuery);
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $queryResult = $stmt->fetchAll();
            return $queryResult;
        } else {
            return 0;
        }
    }

    /**
     * @param $data
     * @param $administration
     * @param $region
     * @return array|mixed[]
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getNombreService($data, $administration, $region)
    {
        if (($administration != null) && ($region != null)) {
            return ['total' => 0];
        } else {
            $whereExist = true;
            $conn = $this->getEntityManager()->getConnection();
            $query = "SELECT COUNT(distinct(p.id)) as total from public.prm_projet p
            inner join prm_user pu on p.created_by_id = pu.id
            inner join prm_administration pa on pu.administration_id = pa.id
            inner join prm_affectation_projet pap on (p.id = pap.projet_id and pap.profil_id = pu.profil_id)
            ";
            $query = $this->queryZone($data, $query);
            $query .= " where p.pdm_date_lancement_os is not null";
            $lastQuery = "";
            $query = $this->kpiFilterV2($query, $whereExist, $data, $administration, $region, $lastQuery);
            if ($data['user']->getAdministration()->getTypeAdmin()->getId() == 1) {
                $query .= ' AND pu.administration_id = ' . $data['user']->getAdministration()->getId();
            }
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $queryResult = $stmt->fetchAll();
            return $queryResult;
        }
    }

    /**
     * @param $query
     * @param bool $whereExist
     * @param $data
     * @param $administration
     * @param $region
     * @return string*
     */
    public function kpiFilterV2($query, $whereExist = false, $data, $administration, $region, $lastQuery = "")
    {
        //-- zone geo --
        if (($data['region'] != null) && ($data['region'] != -1)) {
            if ($whereExist == false) {
                $query .= " where pzg.left_bound >= (select left_bound from prm_zone_geo where id = " . $data['region'] . ")
   AND pzg.right_bound <= (select right_bound from prm_zone_geo where id = " . $data['region'] . ")";
                $whereExist = true;
            } else {
                $query .= " AND pzg.left_bound >= (select left_bound from prm_zone_geo where id = " . $data['region'] . ")
   AND pzg.right_bound <= (select right_bound from prm_zone_geo where id = " . $data['region'] . ")";
            }
        }
        //-- nom --
        if (($data['nomProjet'] != null) && (trim($data['nomProjet']) != "") && ($data['nomProjet'] != -1)) {
            if ($whereExist == false) {
                $query .= " WHERE (UPPER(p.nom) like UPPER('%" . $data['nomProjet'] . "%'))";
                $whereExist = true;
            } else {
                $query .= " AND (UPPER(p.nom) like UPPER('%" . $data['nomProjet'] . "%'))";
            }
        }
        //-- type_administration --
        if (($data['typeAdmin'] != null) && $data['typeAdmin'] != -1) {
            if ($whereExist == false) {
                $query .= " WHERE (pa.type_admin_id = " . $data['typeAdmin'] . ")";
                $whereExist = true;
            } else {
                $query .= " AND (pa.type_admin_id = " . $data['typeAdmin'] . ")";
            }
        }
        //-- administration --
        if (($data['administration'] != null) && $data['administration'] != -1) {
            if ($whereExist == false) {
                $query .= " WHERE (pu.administration_id = " . $data['administration'] . ")";
                $whereExist = true;
            } else {
                $query .= " AND (pu.administration_id = " . $data['administration'] . ")";
            }
        }
        //-- statut projet --
        if (($data['statut'] != null) && $data['statut'] != -1) {
            if ($whereExist == false) {
                $query .= " WHERE (p.statut_id = " . $data['statut'] . ")";
                $whereExist = true;
            } else {
                $query .= " AND (p.statut_id = " . $data['statut'] . ")";
            }
        }
        //-- pdm_date_fin_prevu --
        if (($data['dateFinPrevdebut'] != null) && ($data['dateFinPrevdebut'] != -1) && ($data['dateFinPrevfin'] != null) && ($data['dateFinPrevfin'] != -1)) {
            if ($whereExist == false) {
                $query .= " WHERE cast(p.pdm_date_fin_prevu as date) >= cast('" . $data['dateFinPrevdebut'] . "' as date) AND cast(p.pdm_date_fin_prevu as date) <= cast('" . $data['dateFinPrevfin'] . "' as date)";
                $whereExist = true;
            } else {
                $query .= " AND cast(p.pdm_date_fin_prevu as date) >= cast('" . $data['dateFinPrevdebut'] . "' as date) AND cast(p.pdm_date_fin_prevu as date) <= cast('" . $data['dateFinPrevfin'] . "' as date)";
            }
        }
        //-- en retard --
        if (($data['enRetard'] !== null) && ($data['enRetard'] !== -1)) {
            if ($whereExist == false) {
                $query .= " WHERE (p.en_retard = cast(" . $data['enRetard'] . " as bool))";
                $whereExist = true;
            } else {
                $query .= " AND (p.en_retard = cast(" . $data['enRetard'] . " as bool))";
            }
        }
        //-- engagement --
        if (($data['engagement'] != null) && $data['engagement'] != -1) {
            if ($whereExist == false) {
                $query .= " WHERE (p.engagement_id = " . $data['engagement'] . ")";
                $whereExist = true;
            } else {
                $query .= " AND (p.engagement_id = " . $data['engagement'] . ")";
            }
        }
        //-- secteur --
        if (($data['secteur'] != null) && $data['secteur'] != -1) {
            if ($whereExist == false) {
                $query .= " WHERE (p.secteur_id = " . $data['secteur'] . ")";
                $whereExist = true;
            } else {
                $query .= " AND (p.secteur_id = " . $data['secteur'] . ")";
            }
        }
        //-- projet_inaugurable --
        if (($data['inaugurable'] !== null) && ($data['inaugurable'] !== -1)) {
            if ($whereExist == false) {
                $query .= " WHERE (p.inaugurable = cast(" . $data['inaugurable'] . " as bool))";
                $whereExist = true;
            } else {
                $query .= " AND (p.inaugurable = cast(" . $data['inaugurable'] . " as bool))";
            }
        }

        if (($administration != null) || ($region != null)) {
            $queryAdmin = "";
            $queryRegion = "";
            if ($whereExist == false) {
                if ($administration != null) {
                    $queryAdmin = 'pu.administration_id = ' . $administration;
                    $whereOr = true;
                }
                if ($whereOr == true) {
                    $queryRegion = ' or pu.region_id = ' . $region;
                    $queryRegion = ($region != null) ? $queryRegion : '';
                } else {
                    $queryRegion = 'pu.region_id = ' . $region;
                    $queryRegion = ($region != null) ? $queryRegion : '';
                }
                $q = $queryAdmin . $queryRegion;
                $query .= " WHERE ($q)";
                $whereExist = true;
            } else {
                if ($administration != null) {
                    $queryAdmin = 'pu.administration_id = ' . $administration;
                    $whereOr = true;
                }
                if ($whereOr == true) {
                    $queryRegion = ' or pu.region_id = ' . $region;
                    $queryRegion = ($region != null) ? $queryRegion : '';
                } else {
                    $queryRegion = 'pu.region_id = ' . $region;
                    $queryRegion = ($region != null) ? $queryRegion : '';
                }
                $q = $queryAdmin . $queryRegion;
                $query .= " AND ($q)";
            }
        }

        if ($whereExist == false) {
            $query .= " WHERE (p.valide = true) and (p.projet_id is null)";
            $whereExist = true;
        } else {
            $query .= " AND (p.valide = true) and (p.projet_id is null)";
        }

        $query .= $lastQuery;
        return $query;
    }

    /**
     * @param $idProjet
     * @return mixed[]
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function listSousProjet($idProjet)
    {
        $conn = $this->getEntityManager()->getConnection();
        $query = "SELECT distinct(p.id) as id
 FROM public.prm_projet p
    where p.projet_id = $idProjet";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $response = $stmt->fetchAll();
        return $response;
    }

    /**
     * @param $aIdProjet
     * @return mixed[]
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getProjetParentById($aIdProjet)
    {
        $string = implode("','", $aIdProjet);
        $conn = $this->getEntityManager()->getConnection();
        $query = "select p.id as id from prm_projet p where p.projet_id in ('" . $string . "') limit " . count($aIdProjet);
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
