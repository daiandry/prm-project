<?php

namespace App\Repository;

use App\Entity\PrmSecteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PrmSecteur|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrmSecteur|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrmSecteur[]    findAll()
 * @method PrmSecteur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrmSecteurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrmSecteur::class);
    }

    public function getAllSecteur()
    {
        $query = $this->createQueryBuilder('ps');
        $query = $query->getQuery();
        $query->useResultCache(true, $_ENV['LIFETIME_CACHE'], 'list_secteur');
        return $query->getResult();
    }
}
