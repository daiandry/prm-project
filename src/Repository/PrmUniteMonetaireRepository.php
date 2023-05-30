<?php

namespace App\Repository;

use App\Entity\PrmUniteMonetaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PrmUniteMonetaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrmUniteMonetaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrmUniteMonetaire[]    findAll()
 * @method PrmUniteMonetaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrmUniteMonetaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrmUniteMonetaire::class);
    }

    // /**
    //  * @return PrmUniteMonetaire[] Returns an array of PrmUniteMonetaire objects
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
    public function findOneBySomeField($value): ?PrmUniteMonetaire
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
