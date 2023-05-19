<?php
/**
 * Created by PhpStorm.
 * User: nambinina2
 * Date: 11/11/2020
 * Time: 14:07
 */

namespace App\EventListener;


use App\Entity\PrmProfil;
use App\Entity\User;
use Doctrine\Persistence\Event\LifecycleEventArgs;

/**
 * Class ProfilListener
 * @package App\EventListener
 */
class ProfilListener
{

    /**
     * @param PrmProfil $profil
     * @param LifecycleEventArgs $eventArgs
     */
    public function postUpdate(PrmProfil $profil, LifecycleEventArgs $eventArgs)
    {

        if (!$profil) {
            return;
        }

        $droits = $profil->getDroits();
        $em = $eventArgs->getObjectManager();
        $em->getEventManager()->removeEventListener(['postUpdate'], $this);
        if (count($droits) > 0) {
            $users = $em->getRepository(User::class)->findBy(['profil' => $profil]);
            foreach($users as $user) {
                $user->setRoles([]);
                foreach ($droits as $droit) {
                    $user->addRole($droit->getCode());
                    $em->persist($user);
                    $em->flush();
                }
            }
        }

    }
}