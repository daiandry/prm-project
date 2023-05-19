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
use App\Event\ProjetEvent;
use App\Repository\PrmAffectationProjetRepository;
use App\Service\CommunService;
use App\Service\Mailer;
use App\Service\ProjetService;
use App\Utils\ConstantSrv;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Templating\EngineInterface;

/**
 * Class ProfilListener
 * @package App\EventListener
 */
class ProjetListener
{
    private $projetService;
    private $security;
    private $repAffectionProjet;
    private $templating;
    private $mailer;
    private $request;
    public function __construct(ProjetService $projetService, Security $security, PrmAffectationProjetRepository $prmAffectationProjetRepository, EngineInterface $engine, Mailer $mailer, RequestStack $requestStack)
    {
        $this->projetService = $projetService;
        $this->security = $security;
        $this->repAffectionProjet = $prmAffectationProjetRepository;
        $this->templating = $engine;
        $this->mailer = $mailer;
        $this->request = $requestStack;
    }

    /**
     * @param PrmTaches $taches
     * @param LifecycleEventArgs $eventArgs
     */
    public function onChangedSendMail(ProjetEvent $projetEvent)
    {
        $projet = $projetEvent->getProjet();
        if(!($this->request->getCurrentRequest()->get('_route') === "api_edit_project")) {
            return;
        }

        if ($projet instanceof PrmProjet) {
            $user = $this->security->getUser();

            $userProfils = $this->repAffectionProjet->findProfilsByProjetAndUser($projet, $user);
            $aProfilsId = [];
            foreach ($userProfils as $item => $profils) {
                foreach ($profils as $idProfil) {
                    array_push($aProfilsId, $idProfil);
                }
            }
            $nomUser = $user->getNom()."( ".$user->getEmail()." )";
            $body = $this->templating->render('projet/projet_send_mail_modification.html.twig', [
                'nom' => $projet->getNom()."( ".$projet->getSoaCode()." )",
                'list_validateur' => [$nomUser],
                'texte' => "a été modifié par $nomUser",
                'id_projet' => $projet->getId(),
            ]);

            $mailCollab = $this->projetService
                ->getMailParcourProjet($projet, null, null, true, $user, $aProfilsId);
            if (!empty($mailCollab)) {
                $this->mailer->sendMailWithoutParm($mailCollab, $subject = "Projet modifié", $body);
            }
        }
    }
}