<?php
/**
 * Created by PhpStorm.
 * User: nambinina2
 * Date: 11/11/2020
 * Time: 14:07
 */

namespace App\EventListener;


use App\Entity\PrmProfil;
use App\Entity\PrmProjet;
use App\Entity\PrmTaches;
use App\Entity\User;
use App\Service\CommunService;
use App\Utils\ConstantSrv;
use Doctrine\Persistence\Event\LifecycleEventArgs;

/**
 * Class ProfilListener
 * @package App\EventListener
 */
class TachesListener
{
    protected $commun;

    public function __construct(CommunService $commun)
    {
        $this->commun = $commun;
    }

    /**
     * @param PrmTaches $taches
     * @param LifecycleEventArgs $eventArgs
     */
    public function postPersist(PrmTaches $taches, LifecycleEventArgs $eventArgs)
    {
        if (!$taches) {
            return;
        }

        $projet = $taches->getProjet();
        $em = $eventArgs->getObjectManager();
        $em->getEventManager()->removeEventListener(['postUpdate'], $this);

        if ($projet instanceof PrmProjet) {
            $taches->getCategorie()->getId();
            $tachesProjet = $projet->getTaches();
            $montantDepenseMandate = $projet->getRfMontantDepensesDecaissessMandate();
            $montantDepenseLiquide = $projet->getRfMontantDepensesDecaissessLiquide();
            $montantMandate = $montantDepenseMandate;
            $montantLiquide = $montantDepenseLiquide;
            $montantBudgetConsome = 0;

            foreach ($tachesProjet as $tache) {
                if ($typeTache = $tache->getTypeTache()) {
                    if ($typeTache->getId() == ConstantSrv::MONTANT_DECAISSE_MANDATE) {
                        $montantDepenseMandate += (float)$tache->getValeurReel();
                    } elseif ($typeTache->getId() == ConstantSrv::MONTANT_DECAISSE_LIQUIDE) {
                        $montantDepenseLiquide += (float)$tache->getValeurReel();
                    } elseif ($typeTache->getId() == ConstantSrv::MONTANT_BUDGET_CONSOMME) {
                        $montantBudgetConsome += (float)$tache->getValeurReel();
                    }
                }
            }

            $projet->setRfMontantDepensesDecaissessMandate($montantDepenseMandate);
            $projet->setRfMontantDepensesDecaissessLiquide($montantDepenseLiquide);
            $projet->setRfBudgetConsomme($montantDepenseLiquide);
            $em->persist($projet);
            $em->flush();
            if (($montantMandate != $montantDepenseMandate) || ($montantLiquide != $montantDepenseLiquide)) {
                $this->commun->setHistoriqueAvancementProjet($projet);
            }
        }
    }
}