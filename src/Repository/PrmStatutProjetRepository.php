<?php

namespace App\Repository;

use App\Entity\PrmStatutProjet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PrmStatutProjetRepository|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrmStatutProjetRepository|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrmStatutProjetRepository[]    findAll()
 * @method PrmStatutProjetRepository[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrmStatutProjetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrmStatutProjet::class);
    }

    /**
     * @return mixed
     */
    public function getAllPStatus()
    {
        $query = $this->createQueryBuilder('sp');

        return $query->getQuery()
            ->setResultCacheId('list_status_projet')->useQueryCache(true)->setResultCacheLifetime($_ENV['LIFETIME_CACHE'])
            ->getResult();
    }
}
