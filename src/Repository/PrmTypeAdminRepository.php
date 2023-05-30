<?php

namespace App\Repository;

use App\Entity\PrmTypeAdmin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PrmTypeAdmin|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrmTypeAdmin|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrmTypeAdmin[]    findAll()
 * @method PrmTypeAdmin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrmTypeAdminRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrmTypeAdmin::class);
    }

    // /**
    //  * @return PrmTypeAdmin[] Returns an array of PrmTypeAdmin objects
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
    public function findOneBySomeField($value): ?PrmTypeAdmin
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
