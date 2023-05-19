<?php
/**
 * Created by PhpStorm.
 * User: nambinina2
 * Date: 11/11/2020
 * Time: 14:07
 */

namespace App\EventListener;


use App\Entity\PrmCategorieProjet;
use Doctrine\Persistence\Event\LifecycleEventArgs;

/**
 * Class CategorieProjetListener
 * @package App\EventListener
 */
class CategorieProjetListener
{
    private $cacheDriver;

    public function __construct($cacheDriver)
    {
        $this->cacheDriver = $cacheDriver;
    }

    public function postPersist(PrmCategorieProjet $c, LifecycleEventArgs $args)
    {
        $this->cacheDriver->expire('[list_categorie_projet][1]', 0);
    }

    public function postUpdate(PrmCategorieProjet $c, LifecycleEventArgs $args)
    {
        $this->cacheDriver->expire('[list_categorie_projet][1]', 0);
    }
}