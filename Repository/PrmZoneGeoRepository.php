<?php

namespace App\Repository;

use App\Entity\PrmZoneGeo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PrmZoneGeo|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrmZoneGeo|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrmZoneGeo[]    findAll()
 * @method PrmZoneGeo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrmZoneGeoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrmZoneGeo::class);
    }

    /**
     * @param $idZone
     */
    public function getFatherAndSon($idZone, $idType = null)
    {
        $conn = $this->getEntityManager()->getConnection();
        $query = "SELECT id,type_id,libelle,code,parent_id
FROM   prm_zone_geo
WHERE  left_bound > (select left_bound from prm_zone_geo where id = $idZone)
   AND right_bound < (select right_bound from prm_zone_geo where id = $idZone)";
        if ($idType != null) {
            $query .= " and type_id=$idType";
        }
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @param $idZone
     */
    public function getZoneById($idZone)
    {
        $conn = $this->getEntityManager()->getConnection();
        $query = "select z.id as zone_id,t.id as type_id,t.libelle as type_libelle,z.libelle as libelle_zone,z.code as code_zone,z.geo_ref,z.parent_id from prm_zone_geo z
inner join prm_type_zone t on t.id = z.type_id
where z.id = $idZone";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * @param $idZone
     * @return mixed
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getTypeZone($idZone)
    {
        $conn = $this->getEntityManager()->getConnection();
        $query = "select type_id as type from prm_zone_geo z
inner join prm_type_zone t on t.id = z.type_id
where z.id = $idZone";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch();
    }
}
