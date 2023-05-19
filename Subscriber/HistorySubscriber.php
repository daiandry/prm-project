<?php
/**
 * Created by PhpStorm.
 * User: nambinina2
 * Date: 19/09/2019
 * Time: 17:46.
 */

namespace App\Subscriber;

use App\Annotation\TrackableReader;
use App\Entity\ActionType;
use App\Entity\PrmAdministration;
use App\Entity\PrmProjet;
use App\Entity\PrmTaches;
use App\Entity\PrmZoneGeo;
use App\Entity\RessourceType;
use App\Entity\TraceLog;
use App\Entity\User;
use App\Service\CommunService;
use App\Service\Mailer;
use App\Utils\ConstantSrv;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Twig\Environment;

class HistorySubscriber implements EventSubscriber
{
    private $serializer;
    private $container;
    private $security;
    private $trackable;
    private $templating;
    private $mailer;
    private $commun;

    /**
     * HistorySubscriber constructor.
     * @param Container $container
     * @param Security $security
     * @param TrackableReader $trackableReader
     * @throws \Exception
     */
    public function __construct(Container $container, Security $security, TrackableReader $trackableReader, Environment $twig, Mailer $mailer, CommunService $commun)
    {
        $this->container = $container;
        $this->serializer = $container->get('serializer');
        $this->security = $security;
        $this->trackable = $trackableReader;
        $this->token_storage = $security;
        $this->templating = $twig;
        $this->mailer = $mailer;
        $this->commun = $commun;
    }

    /**
     * @return array|string[]
     */
    public function getSubscribedEvents()
    {
        return [
            'preUpdate',
        ];
    }

    /**
     * @param PreUpdateEventArgs $eventArgs
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     */
    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();
        $unitOfWork = $em->getUnitOfWork();
        $entities = $unitOfWork->getScheduledEntityUpdates();
        //attribut array collection
        $collections = $unitOfWork->getScheduledCollectionUpdates();

        $dataArrayCollection = [];
        foreach ($collections as $collection) {
            $subObjectInserts = $collection->getInsertDiff();
            $subObjectDeletes = $collection->getDeleteDiff();
            foreach ($subObjectInserts as $subInsert) {
                $dataArrayCollection[] = $subInsert;
            }
            foreach ($subObjectDeletes as $subDeleted) {
                $dataArrayCollection[] = $subDeleted;
            }
        }
        //supprimer cet event pour eviter de boucler sur flush lors de la migration de history

        $em->getEventManager()->removeEventListener(['preUpdate'], 'app.history');
        foreach ($entities as $entity) {
            // ne fait rien si l'entite n'est pas trackable
            if (!$this->trackable->isTrackable($entity)) {
                return;
            }
            $changesets = $unitOfWork->getEntityChangeSet($entity);
            $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
            $encoder = new JsonEncoder();

            $normalizer = new ObjectNormalizer($classMetadataFactory);

            $serializer = new Serializer([new DateTimeNormalizer('d/m/Y'), $normalizer], [$encoder]);
            $changesets = array_merge($changesets, $dataArrayCollection);
            if ($entity instanceof PrmTaches) {
                $this->checkTacheEdit($entity, $em);
                continue;
            } else {
                // serialize data changesets
                $data = $serializer->normalize($changesets, 'json', ['groups' => ['log:write']]);
                $dataserialize = $serializer->serialize($data, 'json');

                //get class name
                $nameClass = $em->getMetadataFactory()->getMetadataFor(get_class($entity))->getName();
                $nameWithOutNameSpace = join('', array_slice(explode('\\', $nameClass), -1));

                // creation ressouce
                $ressourceType = new RessourceType();
                $ressourceType->setLibelle($nameWithOutNameSpace);
                $em->persist($ressourceType);

                //trace
                if (!$this->security->getUser()) {
                    return;
                }
                $history = new TraceLog();
                $history->setClasseName($nameWithOutNameSpace);
                $history->setRessourceType($ressourceType);
                $history->setMetadata($data);
                $history->setRessourceId($entity->getId());
                $history->setUser($this->security->getUser());
                $em->persist($history);
                $unitOfWork->computeChangeSets();
                $metaData = $em->getClassMetadata('App\Entity\TraceLog');
                $unitOfWork->computeChangeSet($metaData, $history);
                $em->flush();
                if (($entity === $this->security->getUser()) && isset($changesets['email'])) {
                    $mailAdmin = $this->getMailAdmin($eventArgs);
                    $mailCreator = $this->security->getUser()->getCreatedBy()?$this->security->getUser()->getCreatedBy()->getEmail():null;
                    $this->sendMailOnEmailChange($changesets['email'], $this->security->getUser(), $mailAdmin, $mailCreator);
                }
            }
        }
    }

    /**
     * @param $data
     * @param User $user
     * @param $mailAdmin
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function sendMailOnEmailChange($data, User $user, $mailAdmin, $mailCreator = null)
    {
        $objet = "Changement mail";
        $email = $user->getEmail();
        $ancienMail = $data[0];
        $nouveauMail = $data[1];
        $lienProfil = (isset($_ENV['HOST_FRONT_LIEN_PROFIL_USER'])) ? str_replace(':idUser', $user->getId(), $_ENV['HOST_FRONT_LIEN_PROFIL_USER']) : "localhost";
        $lienConfirmMail = (isset($_ENV['HOST_FRONT_LIEN_PROFIL_USER'])) ? str_replace(':idUser', $user->getId(), $_ENV['HOST_FRONT_LIEN_PROFIL_USER']) : "localhost";
        $bodyMailUser = $this->templating->render('user/mailing-modif-mail-user.html.twig', [
            'ancien' => $ancienMail,
            'nouveau' => $nouveauMail,
            'lien' => $lienConfirmMail
        ]);
        $bodyMailAdmin = $this->templating->render('user/mailing-modif-mail-admin.html.twig', [
            'nom' => $user->getNom(),
            'prenom' => '',
            'administration' => ($user->getAdministration() instanceof PrmAdministration) ? $user->getAdministration()->getLibelle() : '',
            'localisation' => ($user->getRegion() instanceof PrmZoneGeo) ? $user->getRegion()->getLibelle() : '',
            'ancien' => $ancienMail,
            'nouveau' => $nouveauMail,
            'lien_profil' => $lienProfil
        ]);

        $this->mailer->sendMailWithoutParm($data, $objet, $bodyMailUser);
        $this->mailer->sendMailWithoutParm($mailAdmin, $objet, $bodyMailAdmin, $mailCreator);
    }

    /**
     * @param PreUpdateEventArgs $eventArgs
     * @return array
     */
    public function getMailAdmin(PreUpdateEventArgs $eventArgs)
    {
        $aMailAdmin = [];
        $em = $eventArgs->getEntityManager();
        $rep = $em->getRepository(User::class);
        $allAdmin = $rep->findBy(array('profil' => ConstantSrv::ROLE_ADMIN));
        if (!empty($allAdmin)) {
            foreach ($allAdmin as $admin) {
                array_push($aMailAdmin, $admin->getEmail());
            }
        }
        return $aMailAdmin;
    }

    /**
     * @param PrmTaches $taches
     * @param $em
     */
    public function checkTacheEdit(PrmTaches $taches, $em)
    {
        $projet = $taches->getProjet();
        if ($projet instanceof PrmProjet) {
            $montantDepenseMandate = $projet->getRfMontantDepensesDecaissessMandate();
            $montantDepenseLiquide = $projet->getRfMontantDepensesDecaissessLiquide();
            $montantMandate = $montantDepenseMandate;
            $montantLiquide = $montantDepenseLiquide;
            $montantBudgetConsome = 0;
            $montantDepenseMandateNew = 0;
            $montantDepenseLiquideNew = 0;
            $tachesProjet = $projet->getTaches();
            foreach ($tachesProjet as $tache) {
                if ($typeTache = $tache->getTypeTache()) {
                    if ($typeTache->getId() == ConstantSrv::MONTANT_DECAISSE_MANDATE) {
                        $montantDepenseMandateNew += (float)$tache->getValeurReel();
                    } elseif ($typeTache->getId() == ConstantSrv::MONTANT_DECAISSE_LIQUIDE) {
                        $montantDepenseLiquideNew += (float)$tache->getValeurReel();
                    } elseif ($typeTache->getId() == ConstantSrv::MONTANT_BUDGET_CONSOMME) {
                        $montantBudgetConsome += (float)$tache->getValeurReel();
                    }
                }
            }
            $projet->setRfMontantDepensesDecaissessMandate($montantDepenseMandateNew);
            $projet->setRfMontantDepensesDecaissessLiquide($montantDepenseLiquideNew);
            $projet->setRfBudgetConsomme($montantDepenseLiquideNew);
            $em->persist($projet);
            $em->flush();
            if (($montantMandate != $montantDepenseMandate) || ($montantLiquide != $montantDepenseLiquide)) {
                $this->commun->setHistoriqueAvancementProjet($projet);
            }
        }
    }
}
