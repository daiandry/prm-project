<?php

namespace App\Repository;

use App\Entity\PrmDocType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PrmDocType|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrmDocType|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrmDocType[]    findAll()
 * @method PrmDocType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrmDocTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrmDocType::class);
    }

    // /**
    //  * @return PrmDocType[] Returns an array of PrmDocType objects
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
    public function findOneBySomeField($value): ?PrmDocType
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
