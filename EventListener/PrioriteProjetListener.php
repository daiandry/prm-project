<?php
/**
 * Created by PhpStorm.
 * User: nambinina2
 * Date: 11/11/2020
 * Time: 14:07
 */

namespace App\EventListener;


use App\Entity\PrmPrioriteProjet;
use Doctrine\Persistence\Event\LifecycleEventArgs;

/**
 * Class PrioriteProjetListener
 * @package App\EventListener
 */
class PrioriteProjetListener
{
    private $cacheDriver;

    public function __construct($cacheDriver)
    {
        $this->cacheDriver = $cacheDriver;
    }

    public function postPersist(PrmPrioriteProjet $p, LifecycleEventArgs $args)
    {
        $this->cacheDriver->expire('[list_priorite_projet][1]', 0);
    }

    public function postUpdate(PrmPrioriteProjet $p, LifecycleEventArgs $args)
    {
        $this->cacheDriver->expire('[list_priorite_projet][1]', 0);
    }
}