<?php

namespace App\Repository;

use App\Entity\PrmHistoriqueAvancement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PrmHistoriqueAvancement|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrmHistoriqueAvancement|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrmHistoriqueAvancement[]    findAll()
 * @method PrmHistoriqueAvancement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrmHistoriqueAvancementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrmHistoriqueAvancement::class);
    }

    /**
     * @param $idProjet
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function listHistoriqueAvancementProjet($idProjet, $page = 1, $limit = 5)
    {
        $qb = $this->createQueryBuilder('h')
            ->andWhere('h.projet = :projet')
            ->setParameter('projet', $idProjet);
        $qbTotal = clone $qb;
        $qbTotal->select('COUNT(h) total');
        $total = $qbTotal->getQuery()->getOneOrNullResult();
        $paginate = false;
        $debut = 1;
        if (!is_null($page) && !empty($page)) {
            $debut = $page;
        }
        if (!is_null($limit) && !empty($limit)) {
            $paginate = true;
        } else {
            if (!is_null($page) && !empty($page)) {
                $limit = 100;
                $paginate = true;
            }
        }
        if ($paginate) {
            $qb->setFirstResult($limit * max(0, $debut - 1));
            $qb->setMaxResults($limit);
        }
        $queryResult = $qb->getQuery()->getResult();
        $response = array(
            'total' => (int)$total['total'],
            'list' => $queryResult,
        );
        return $response;
    }
}
