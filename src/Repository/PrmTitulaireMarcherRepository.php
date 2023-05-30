<?php

namespace App\Repository;

use App\Entity\PrmTitulaireMarcher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PrmTitulaireMarcher|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrmTitulaireMarcher|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrmTitulaireMarcher[]    findAll()
 * @method PrmTitulaireMarcher[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrmTitulaireMarcherRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrmTitulaireMarcher::class);
    }

    // /**
    //  * @return PrmTitulaireMarcher[] Returns an array of PrmTitulaireMarcher objects
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
    public function findOneBySomeField($value): ?PrmTitulaireMarcher
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
