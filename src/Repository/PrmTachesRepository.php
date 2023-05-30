<?php

namespace App\Repository;

use App\Entity\PrmProjet;
use App\Entity\PrmTaches;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

/**
 * @method PrmTaches|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrmTaches|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrmTaches[]    findAll()
 * @method PrmTaches[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrmTachesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrmTaches::class);
    }

    public function historiqueAvancementProjet(PrmProjet $projet)
    {
        $query = $this->createQueryBuilder('pt');

        $query->select('SUM(cast(pt.valeurReel as float)) as somme')
                ->innerJoin('pt.projet', 'p')
                ->andWhere(
                    $query->expr()->eq('p.id',':projetId')
                )
                ->setParameter('projetId',$projet->getId())
                ->innerJoin('pt.typeTache', 'tp')
                ->andWhere(
                    $query->expr()->in('tp.id',array(4,5))
                )
        ;

        $result = $query->getQuery();

        $somme =  $result->getResult()[0]['somme'];
        return $somme;
    }

    public function getTachesByProjet(int $page = 1, $itemPerPage, $projet): Paginator
    {
        $firstResult = ($page - 1) * $itemPerPage;
        $queryBuilder = $this->createQueryBuilder('t');
            $queryBuilder
                ->andWhere(
                    $queryBuilder->expr()->eq('t.projet', ':projet')
                )
                ->setParameter('projet', $projet);
        $queryBuilder->orderBy('t.id', 'DESC');
        $query = $queryBuilder->getQuery()
            ->setFirstResult($firstResult)
            ->setMaxResults($itemPerPage);

        $doctrinePaginator = new DoctrinePaginator($query);
        $paginator = new Paginator($doctrinePaginator);
        return $paginator;
    }
}
