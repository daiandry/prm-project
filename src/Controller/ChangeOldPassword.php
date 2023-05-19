<?php
/**
 * Created by PhpStorm.
 * User: nambinina2
 * Date: 29/10/2020
 * Time: 11:49
 */

namespace App\Controller;


use App\Entity\User;
use App\Service\Mailer;
use App\Utils\Fonctions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

/**
 * Class ResetPassword
 * @package App\Controller
 */
class ChangeOldPassword
{
    private $em;
    private $user;
    private $mailer;
    private $templating;
    private $token_storage;

    public function __construct(EntityManagerInterface $entityManager, Security $security, Mailer $mailer,Environment $twig)
    {
        $this->em = $entityManager;
        $this->user = $security->getUser();
        $this->token_storage = $security;
        $this->mailer = $mailer;
        $this->templating = $twig;
    }

    /**
     * @param User $data
     * @return JsonResponse
     */
    public function __invoke(User $data)
    {
        $oldPass = $data->getPassword();
        $response = new JsonResponse();

        if (Fonctions::checkCredential($this->user, $oldPass)) {

            if ($data->getPlainPassword()) {
                $this->user->setPassword(
                    $data->getPlainPassword()
                );
                $this->user->setConfirmationToken(null);
                $this->user->eraseCredentials();
                $this->em->persist($this->user);
                $this->em->flush();
                $this->sendMailRessetingPassword($this->user);
                return $response->setData(['code' => 200, 'message' => 'OK']);
            }

            return $response->setData(['code' => 400, 'message' => 'Bad request']);

        } else {
            return $response->setData(['code' => 401, 'message' => 'Bad credential']);
        }
        $token = $data->getConfirmationToken();

    }

    /**
     * @param User $user
     */
    public function sendMailRessetingPassword(User $user)
    {
        $emailUser = $user->getEmail();
        $hasUserCreator = false;
        if ($userCreator = $user->getCreatedBy()) {
            $emailCreator =  $userCreator->getEmail();
            $hasUserCreator = true;
        }

        $token = $this->token_storage->getToken()->getCredentials();
        $bodyUser = $this->templating->render('user/mailing-modif-pass-user.html.twig', [
            'lien' =>$_ENV['HOST_FRONT_CONFIRMATION'].'/'.$token,
            'no_link' => true
        ]);

        $bodyCreator = $this->templating->render('user/mailing-modif-pass-user.html.twig', [
            'lien' =>$_ENV['HOST_FRONT_CONFIRMATION'].'/'.$token,
            'has_creator' => $hasUserCreator,
            'nom_user' => $user->getNom(),
            'no_link' => true
        ]);

        $this->mailer->sendMailWithoutParm($emailUser, "Changement de mot de passe", $bodyUser);
        $this->mailer->sendMailWithoutParm($emailCreator, "Changement de mot de passe", $bodyCreator);
    }
}