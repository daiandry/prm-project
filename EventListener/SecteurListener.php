<?php
/**
 * Created by PhpStorm.
 * User: nambinina2
 * Date: 11/11/2020
 * Time: 14:07
 */

namespace App\EventListener;


use App\Entity\PrmSecteur;
use Doctrine\Persistence\Event\LifecycleEventArgs;

/**
 * Class SecteurListener
 * @package App\EventListener
 */
class SecteurListener
{
    private $cacheDriver;

    public function __construct($cacheDriver)
    {
        $this->cacheDriver = $cacheDriver;
    }

    public function postPersist(PrmSecteur $s, LifecycleEventArgs $args)
    {
        $this->cacheDriver->expire('[list_secteur][1]', 0);
    }

    public function postUpdate(PrmSecteur $s, LifecycleEventArgs $args)
    {
        $this->cacheDriver->expire('[list_secteur][1]', 0);
    }
}