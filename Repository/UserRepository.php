<?php

namespace App\Repository;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    const ITEMS_PER_PAGE = 10;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getBooksByFavoriteAuthor(int $page = 1, $itemPerPage = self::ITEMS_PER_PAGE, $nom, $email, $administration, $enabled, $region): Paginator
    {
        $firstResult = ($page - 1) * $itemPerPage;
        $queryBuilder = $this->createQueryBuilder('u');
        if (!is_null($nom) && $nom != "") {
            $queryBuilder
                ->andWhere(
                    $queryBuilder->expr()->like('lower(u.nom)', ':nom')
                )
                ->setParameter('nom', '%' . strtolower($nom) . '%');
        }

        if (!is_null($email) && $email != "") {
            $queryBuilder
                ->andWhere(
                    $queryBuilder->expr()->like('lower(u.email)', ':email')
                )
                ->setParameter('email', '%' . strtolower($email) . '%');
        }

        if (!is_null($administration) && $administration != "") {
            $queryBuilder
                ->andWhere(
                    $queryBuilder->expr()->eq('u.administration', ':administration')
                )
                ->setParameter('administration', $administration);
        }

        if (!is_null($enabled) && $enabled != "") {
            $queryBuilder
                ->andWhere(
                    $queryBuilder->expr()->eq('u.enabled', ':enabled')
                )
                ->setParameter('enabled', $enabled);
        }
        if (!is_null($region) && $region != "" && is_numeric($region)) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->eq('u.region', ':region'))->setParameter('region', $region);
        }
        $queryBuilder->orderBy('u.id', 'DESC');
        $query = $queryBuilder->getQuery()
            ->setFirstResult($firstResult)
            ->setMaxResults($itemPerPage);

        $doctrinePaginator = new DoctrinePaginator($query);
        $paginator = new Paginator($doctrinePaginator);
        return $paginator;
    }

    /**
     * @return mixed[]
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAllUserInfo()
    {
        $conn = $this->getEntityManager()->getConnection();
        $query = "select (select count(*) from prm_user) as total_user,count(pu),pp.id,pp.libelle from prm_user pu
inner join prm_profil pp on pu.profil_id = pp.id
group by pp.id";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @param $idProfil
     * @return mixed[]
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAllUserByProfil($idProfil)
    {
        $conn = $this->getEntityManager()->getConnection();
        $query = "select pu.id,pu.username,pp.id,pp.libelle from prm_user pu
inner join prm_profil pp on pu.profil_id = pp.id where pp.id = $idProfil";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @param $aIdProfil
     * @return mixed[]
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getUserMailByProfil($aIdProfil)
    {
        $string = implode("','", $aIdProfil);
        $conn = $this->getEntityManager()->getConnection();
        $query = "select pu.email from prm_user pu where pu.profil_id in ('" . $string . "')";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @param User $user
     * @param $aProfils
     * @return mixed[]
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function findUserByAdminOrRegionAndProfil(User $user, $aProfils)
    {
        $string = implode("','", $aProfils);
        $conn = $this->getEntityManager()->getConnection();
        if ($user->getAdministration()) {
            $andwhere = "pu.administration_id=".$user->getAdministration()->getId();
        } else {
            $andwhere = "pu.region_id=".$user->getRegion()->getId();
        }
        $query = "select pu.email from prm_user pu where $andwhere and pu.profil_id in ('" . $string . "')";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
