<?php

namespace App\Repository;

use App\Entity\PrmDroit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PrmDroit|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrmDroit|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrmDroit[]    findAll()
 * @method PrmDroit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrmDroitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrmDroit::class);
    }

    /**
     * @param $idDroit
     * @param $idUser
     * @return mixed[]
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getProfilByDroit($idDroit, $idUser)
    {
        $conn = $this->getEntityManager()->getConnection();
        $query = "SELECT pp.* FROM public.prm_droit pd
inner join prm_profil_prm_droit ppd on pd.id = ppd.prm_droit_id
inner join prm_profil pp on pp.id = ppd.prm_profil_id
inner join prm_user pu on pu.profil_id = ppd.prm_profil_id
left join prm_administration pad on pu.administration_id = pad.id
left join prm_zone_geo pzg on pu.region_id = pzg.id
where pd.id = $idDroit group by pp.id";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
