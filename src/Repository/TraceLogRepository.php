<?php
/**
 * Created by PhpStorm.
 * User: nambinina2
 * Date: 20/11/2020
 * Time: 08:49
 */

namespace App\Repository;


use App\Entity\TraceLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TraceLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method TraceLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method TraceLog[]    findAll()
 * @method TraceLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

class TraceLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TraceLog::class);
    }
}