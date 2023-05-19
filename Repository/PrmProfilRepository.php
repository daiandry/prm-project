<?php

namespace App\Repository;

use App\Entity\PrmProfil;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PrmProfil|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrmProfil|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrmProfil[]    findAll()
 * @method PrmProfil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrmProfilRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrmProfil::class);
    }

    // /**
    //  * @return PrmProfil[] Returns an array of PrmProfil objects
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
    public function findOneBySomeField($value): ?PrmProfil
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
