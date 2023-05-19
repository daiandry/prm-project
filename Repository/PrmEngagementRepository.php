<?php

namespace App\Repository;

use App\Entity\PrmEngagement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PrmEngagement|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrmEngagement|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrmEngagement[]    findAll()
 * @method PrmEngagement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrmEngagementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrmEngagement::class);
    }

    public function getAllEngagements()
    {
        $query = $this->createQueryBuilder('pe');
        $query = $query->getQuery();
        $query->useResultCache(true, $_ENV['LIFETIME_CACHE'], 'list_engagement');
        return $query->getResult();
    }
}
