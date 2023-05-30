<?php

namespace App\Repository;

use App\Entity\PrmPhotos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PrmPhotos|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrmPhotos|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrmPhotos[]    findAll()
 * @method PrmPhotos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrmPhotosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrmPhotos::class);
    }

    // /**
    //  * @return PrmPhotos[] Returns an array of PrmPhotos objects
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
    public function findOneBySomeField($value): ?PrmPhotos
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
