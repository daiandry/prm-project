<?php

namespace App\Repository;

use App\Entity\PrmSituationProjet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PrmSituationProjet|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrmSituationProjet|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrmSituationProjet[]    findAll()
 * @method PrmSituationProjet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrmSituationProjetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrmSituationProjet::class);
    }

    // /**
    //  * @return PrmSituationProjet[] Returns an array of PrmSituationProjet objects
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
    public function findOneBySomeField($value): ?PrmSituationProjet
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
