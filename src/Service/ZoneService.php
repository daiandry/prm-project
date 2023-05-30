<?php
/**
 * Created by PhpStorm.
 * User: Da Andry
 * Date: 13/08/2020
 * Time: 15:06
 */

namespace App\Service;


use App\Entity\PrmCategorieProjet;
use App\Entity\PrmEngagement;
use App\Entity\PrmPrioriteProjet;
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

class ZoneService
{
    protected $container;
    protected $entityManager;
    protected $trans;
    protected $security;

    /**
     * SearchService constructor.
     * @param ContainerInterface $container
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ContainerInterface $container, EntityManagerInterface $entityManager, TranslatorInterface $translator, Security $security)
    {
        $this->container = $container;
        $this->entityManager = $entityManager;
        $this->trans = $translator;
        $this->security = $security;
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @return array
     */
    public function getListZoneElementById(ParamFetcher $paramFetcher)
    {
        $rep = $this->entityManager->getRepository(PrmZoneGeo::class);
        $idZone = $paramFetcher->get('zone_id');
        $idType = $paramFetcher->get('type_id');
        $oZone = $rep->findOneBy(array('id' => $idZone));
        if ($oZone instanceof PrmZoneGeo) {
            $data = $rep->getFatherAndSon($idZone, $idType);
            $results = $this->sendZone($data, true);
        } else {
            $results = $this->sendZone(null, false);
        }
        return $results;
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @return array
     */
    public function getZoneById(ParamFetcher $paramFetcher)
    {
        $data = [];
        $rep = $this->entityManager->getRepository(PrmZoneGeo::class);
        $aIdZone = $paramFetcher->get('zone_id');
        if (!empty($aIdZone)) {
            foreach ($aIdZone as $idZone) {
                $oZone = $rep->findOneBy(array('id' => $idZone));
                if ($oZone instanceof PrmZoneGeo) {
                    $zone = $rep->getZoneById($idZone);
                    array_push($data, $zone);
                }
            }
        }
        $results = $this->sendZone($data, true);
        return $results;
    }

    /**
     * @param $geoRef
     * @return mixed|null
     */
    public function encodeGeoRef($geoRef)
    {
        $geoRef = (isset($geoRef)) ? json_decode($geoRef) : null;
        return $geoRef;
    }

    /**
     * @param $data
     * @param bool $zoneExist
     * @return array
     */
    public function sendZone($data = null, $zoneExist = false)
    {
        if ($zoneExist) {
            //$data['geo_ref'] = $this->encodeGeoRef($data['geo_ref']);
            //dump($data);die;
            $results = array('code' => ConstantSrv::CODE_SUCCESS, 'Message' => $this->trans->trans('success_request'), 'data' => $data);
        } else {
            $results = array('code' => ConstantSrv::CODE_DATA_NOTFOUND, 'Message' => $this->trans->trans('zone_not_found'));
        }
        return $results;
    }
}
     