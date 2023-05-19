<?php

namespace App\Repository;

use App\Entity\PrmUserStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PrmUserStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrmUserStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrmUserStatus[]    findAll()
 * @method PrmUserStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrmUserStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrmUserStatus::class);
    }

    // /**
    //  * @return PrmUserStatus[] Returns an array of PrmUserStatus objects
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
    public function findOneBySomeField($value): ?PrmUserStatus
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
