<?php

namespace App\Repository;

use App\Entity\PrmStatutTache;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PrmStatutTache|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrmStatutTache|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrmStatutTache[]    findAll()
 * @method PrmStatutTache[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrmStatutTacheRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrmStatutTache::class);
    }

    // /**
    //  * @return PrmStatutTache[] Returns an array of PrmStatutTache objects
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
    public function findOneBySomeField($value): ?PrmStatutTache
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
