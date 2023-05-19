<?php
/**
 * Created by PhpStorm.
 * User: nambinina2
 * Date: 06/11/2020
 * Time: 16:50
 */

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\User;
use App\Service\Mailer;
use App\Service\ProjetService;
use App\Utils\Fonctions;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Util\TokenGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

class UserDataPersister implements ContextAwareDataPersisterInterface
{
    private $passwordEncoder;
    private $projetService;
    private $directory;
    private $dataPersister;
    private $mailer;
    private $user;
    public function __construct(EntityManagerInterface $dataPersister, UserPasswordEncoderInterface $passwordEncoder, ProjetService $projetService, ContainerInterface $container, Mailer $mailer, Security $security)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->projetService = $projetService;
        $this->directory = $container->getParameter('import_path_photos_user');
        $this->dataPersister = $dataPersister;
        $this->mailer = $mailer;
        $this->user = $security->getUser();

    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof User;
    }

    /**
     * @param User $data
     * @return bool|object|void
     */
    public function persist($data, array $context = [])
    {

        if (isset($context['collection_operation_name']) && $context['collection_operation_name'] === 'post_resetting_password') {
            return $data;
        }
        $data->setUsername($data->getEmail());
        $photo = $data->getPhoto();
        $password = Fonctions::generatePassword();

        if ($photo) {
            if ($photo->getNom() && $photo->getChemin()) {
                $nom = $this->projetService->nameUpload($photo->getNom());
                $photo->setNom($nom);
                $this->projetService->uploadFile($nom, $photo->getChemin(), $this->directory);
                $photo->setChemin($this->directory.$nom);
            }
        }
        $token = null;
        if (isset($context['collection_operation_name']) && $context['collection_operation_name'] == "post") {

            $data->setPassword($this->passwordEncoder->encodePassword($data, $password));
            $data->setPasswordRequestedAt(new \Datetime());
            $data->setCreatedBy($this->user);
            $tokenGenerator = new TokenGenerator();
            $token = $tokenGenerator->generateToken();
            $data->setConfirmationToken($token);
        }

        $this->dataPersister->persist($data);
        $this->dataPersister->flush();
        $this->sendMailOnCreate($data->getEmail(), $context, $password, $token);



    }

    public function remove($data, array $context = [])
    {
        // TODO: Implement remove() method.
    }

    public function sendMailOnCreate($email, $context, $password, $token = null)
    {

        if (isset($context['collection_operation_name']) && $context['collection_operation_name'] == "post") {
            $pathLoginFront = $_ENV['HOST_FRONT_LOGIN'].'/'.$token;
            $this->mailer->sendMailCreation($email, "Nouveau Utilisateur", $pathLoginFront, $password);
        }
    }


}