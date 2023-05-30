<?php

namespace App\Repository;

use App\Entity\PrmCategorieTache;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PrmCategorieTache|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrmCategorieTache|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrmCategorieTache[]    findAll()
 * @method PrmCategorieTache[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrmCategorieTacheRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrmCategorieTache::class);
    }

    // /**
    //  * @return PrmCategorieTache[] Returns an array of PrmCategorieTache objects
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
    public function findOneBySomeField($value): ?PrmCategorieTache
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
