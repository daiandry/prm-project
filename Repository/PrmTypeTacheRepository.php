<?php

namespace App\Repository;

use App\Entity\PrmTypeTache;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PrmTypeTache|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrmTypeTache|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrmTypeTache[]    findAll()
 * @method PrmTypeTache[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrmTypeTacheRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrmTypeTache::class);
    }

    // /**
    //  * @return PrmTypeTache[] Returns an array of PrmTypeTache objects
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
    public function findOneBySomeField($value): ?PrmTypeTache
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
