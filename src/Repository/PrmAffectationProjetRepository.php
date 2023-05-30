<?php

namespace App\Repository;

use App\Entity\PrmAffectationProjet;
use App\Entity\PrmProjet;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PrmAffectationProjet|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrmAffectationProjet|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrmAffectationProjet[]    findAll()
 * @method PrmAffectationProjet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrmAffectationProjetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrmAffectationProjet::class);
    }

    /**
     * @param $idProjet
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findLastAffectationByProjet($idProjet)
    {
        $em = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder = $em
            ->from(PrmAffectationProjet::class, 'a')
            ->select('max(a.niveau)')
            ->where('a.projet = :projet')
            ->setParameter('projet', $idProjet);
        $data = $queryBuilder->getQuery()->getOneOrNullResult();
        return $data;
    }

    /**
     * @param $idProjet
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findAffectationProjet($idProjet)
    {
        $em = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder = $em
            ->from(PrmAffectationProjet::class, 'a')
            ->select('max(a.niveau)')
            ->where('a.projet = :projet')
            ->Andwhere('a.valide = :valide')
            ->Andwhere('a.niveau = :niveau')
            ->setParameter('projet', $idProjet)
            ->setParameter('niveau', 1)
            ->setParameter('valide', true);
        $data = $queryBuilder->getQuery()->getOneOrNullResult();
        return $data;
    }

    /**
     * @param $idProjet
     * @return mixed
     */
    public function getListValidateurProjet($idProjet)
    {
        $em = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder = $em
            ->from(PrmAffectationProjet::class, 'a')
            ->select('a')
            ->innerJoin('a.profil', 'p')
            ->innerJoin('a.user', 'u')
            ->where('a.projet = :projet')
            ->Andwhere('a.profilValide = :valide')
            ->setParameter('projet', $idProjet)
            ->setParameter('valide', true);
        $data = $queryBuilder->getQuery()->getResult();
        return $data;
    }

    /**
     * @param $idProjet
     * @return mixed
     */
    public function getListValidateurProjetInProfil($idProjet, $aProfil)
    {
        $em = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder = $em
            ->from(PrmAffectationProjet::class, 'a')
            ->select('a')
            ->innerJoin('a.profil', 'p')
            ->innerJoin('a.user', 'u')
            ->where('a.projet = :projet')
            ->andWhere($em->expr()->in('p.id', ':profil'))
            ->setParameter('projet', $idProjet)
            ->setParameter('profil', $aProfil);
        $data = $queryBuilder->getQuery()->getResult();
        return $data;
    }

    /**
     * @param $idProjet
     * @return mixed
     */
    public function getAllMailInProfilAffectation($idProjet, $administration, $region,$profilValide=false)
    {
        $whereExist = true;
        $conn = $this->getEntityManager()->getConnection();
        $query = "select distinct(pu.email) from prm_affectation_projet pap
inner join prm_user pu on pu.profil_id = pap.profil_id
where pap.projet_id = $idProjet";

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
        $queryResult = $stmt->fetchAll();
        return $queryResult;
    }

    /**
     * @param PrmProjet $projet
     * @param User $user
     * @return mixed[]
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function findProfilsByProjetAndUser(PrmProjet $projet, User $user)
    {
        $conn = $this->getEntityManager()->getConnection();
        $idUser = $user->getId();
        $idProjet = $projet->getId();
        $query = "SELECT distinct(pap.profil_id)
                    FROM public.prm_affectation_projet pap
                    inner join prm_profil pp on pp.id = pap.profil_id
                    inner join prm_user pu on pu.id = pap.user_id
                    -- inner join prm_projet p on p.id = pap.projet_id
                    where 
                    pap.user_id = $idUser
                    and pap.projet_id =  $idProjet
                    group by pap.profil_id";

        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
