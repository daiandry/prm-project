<?php
/**
 * Created by PhpStorm.
 * User: nambinina2
 * Date: 03/11/2020
 * Time: 11:56
 */

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class PhotoProfilUser
{
    private $em;
    private $security;
    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->em = $entityManager;
        $this->security = $security;
    }

    public function __invoke()
    {
        $user = $this->security->getUser();
        $response = new JsonResponse();
        if ($user) {
            $photo = $user->getPhoto();
            $photoProfil = ['mimetype' => "", 'nom' => '', 'chemin' => ''];
            if ($photo) {
                $photoProfil['mimetype'] = $photo->getMimetype();
                $photoProfil['nom'] = $photo->getNom();
                $file = @file_get_contents($photo->getChemin());
                $base64 = base64_encode($file);
                $photoProfil['chemin'] = $base64;
            }
            return $response->setData(['code' => 200, 'message' => 'Token ok', 'data' => $photoProfil]);
        }

        return $response->setData(['code' => 404, 'message' => 'Token invalid']);

    }
}