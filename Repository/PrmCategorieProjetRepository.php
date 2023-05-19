<?php

namespace App\Repository;

use App\Entity\PrmCategorieProjet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PrmCategorieProjet|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrmCategorieProjet|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrmCategorieProjet[]    findAll()
 * @method PrmCategorieProjet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrmCategorieProjetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrmCategorieProjet::class);
    }

    public function getAllCategorieProjet()
    {
        $query = $this->createQueryBuilder('pc');
        $query = $query->getQuery();
        $query->useResultCache(true, $_ENV['LIFETIME_CACHE'], 'list_categorie_projet');
        return $query->getResult();
    }
}
