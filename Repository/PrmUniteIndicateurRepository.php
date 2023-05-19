<?php

namespace App\Repository;

use App\Entity\PrmUniteIndicateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PrmUniteIndicateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrmUniteIndicateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrmUniteIndicateur[]    findAll()
 * @method PrmUniteIndicateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrmUniteIndicateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrmUniteIndicateur::class);
    }

    // /**
    //  * @return PrmUniteIndicateur[] Returns an array of PrmUniteIndicateur objects
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
    public function findOneBySomeField($value): ?PrmUniteIndicateur
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
