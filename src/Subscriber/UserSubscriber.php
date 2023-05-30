<?php
/**
 * Created by PhpStorm.
 * User: nambinina2
 * Date: 06/11/2020
 * Time: 13:43
 */

namespace App\Subscriber;


use App\Entity\User;
use App\Service\ProjetService;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UserSubscriber implements EventSubscriber
{
    /**
     * @var ProjetService
     */
    private $projetService;

    /**
     * UserSubscriber constructor.
     * @param ProjetService $projetService
     */
    public function __construct(ProjetService $projetService)
    {
        $this->projetService = $projetService;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist => 'prePersist'
        ];
    }


    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        $object = $eventArgs->getObject();
        if (!$object instanceof User) {
            return;
        }
        $photo = $object->getPhoto();
        if (!$photo) {
            return;
        }
//        $this->projetService->
        dump($object);die;
    }

}