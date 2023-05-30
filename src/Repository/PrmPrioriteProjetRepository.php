<?php

namespace App\Repository;

use App\Entity\PrmPrioriteProjet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PrmPrioriteProjet|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrmPrioriteProjet|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrmPrioriteProjet[]    findAll()
 * @method PrmPrioriteProjet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrmPrioriteProjetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrmPrioriteProjet::class);
    }

    public function getAllPrioriteProjet()
    {
        $query = $this->createQueryBuilder('pc');
        $query = $query->getQuery();
        $query->useResultCache(true, $_ENV['LIFETIME_CACHE'], 'list_priorite_projet');
        return $query->getResult();
    }
}
