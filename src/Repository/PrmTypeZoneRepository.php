<?php

namespace App\Repository;

use App\Entity\PrmTypeZone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PrmTypeZone|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrmTypeZone|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrmTypeZone[]    findAll()
 * @method PrmTypeZone[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrmTypeZoneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrmTypeZone::class);
    }

    // /**
    //  * @return PrmTypeZone[] Returns an array of PrmTypeZone objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PrmTypeZone
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
