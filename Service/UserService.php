<?php
/**
 * Created by PhpStorm.
 * User: Da Andry
 * Date: 13/08/2020
 * Time: 15:06
 */

namespace App\Service;


use App\Entity\PrmAffectationProjet;
use App\Entity\PrmCategorieProjet;
use App\Entity\PrmDroit;
use App\Entity\PrmEngagement;
use App\Entity\PrmPrioriteProjet;
use App\Entity\PrmProfil;
use App\Entity\PrmProjet;
use App\Entity\PrmSecteur;
use App\Entity\PrmSituationProjet;
use App\Entity\PrmTypeProjet;
use App\Entity\PrmTypeZone;
use App\Entity\PrmZoneGeo;
use App\Entity\User;
use App\Utils\ConstantSrv;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Request\ParamFetcher;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserService
{
    protected $container;
    protected $entityManager;
    protected $trans;
    protected $security;
    protected $projet;
    protected $templating;
    protected $mailer;

    /**
     * SearchService constructor.
     * @param ContainerInterface $container
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ContainerInterface $container, EntityManagerInterface $entityManager, TranslatorInterface $translator, Security $security, ProjetService $projet, Mailer $mailer)
    {
        $this->container = $container;
        $this->entityManager = $entityManager;
        $this->trans = $translator;
        $this->security = $security;
        $this->projet = $projet;
        $this->templating = $container->get('twig');
        $this->mailer = $mailer;
    }

    /**
     * @return JsonResponse
     */
    public function getNombreUtilisateur()
    {
        try {
            $response = new JsonResponse();
            $result = ['total_user' => 0, 'profil' => []];
            $rep = $this->entityManager->getRepository(User::class);
            $allValue = $rep->getAllUserInfo();
            if (!empty($allValue)) {
                $result['total_user'] = $allValue[0]['total_user'];
                foreach ($allValue as $value) {
                    $format['id'] = $value['id'];
                    $format['libelle'] = $value['libelle'];
                    $format['nombre_user'] = $value['count'];
                    array_push($result['profil'], $format);
                }
            }
            $data = array('code' => ConstantSrv::CODE_SUCCESS, 'Message' => $this->trans->trans('success_request'), 'data' => $result);
            $response->setData($data);
            return $response;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @return JsonResponse
     */
    public function getNombreUtilisateurParProfil(ParamFetcher $paramFetcher)
    {
        try {
            $response = new JsonResponse();
            $idProfil = $paramFetcher->get('profil_id');
            $rep = $this->entityManager->getRepository(User::class);
            $allValue = $rep->getAllUserByProfil($idProfil);
            $data = array('code' => ConstantSrv::CODE_SUCCESS, 'Message' => $this->trans->trans('success_request'), 'data' => $allValue);
            $response->setData($data);
            return $response;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
     