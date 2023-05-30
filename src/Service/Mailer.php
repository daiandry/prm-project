<?php
/**
 * Created by PhpStorm.
 * User: nambinina2
 * Date: 03/11/2020
 * Time: 11:15
 */

namespace App\Service;


use Symfony\Component\DependencyInjection\ContainerInterface;

class Mailer
{
    private $container;
    private $templating;
    private $mailer;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->templating = $container->get('twig');
        $this->mailer = $container->get('mailer');
    }

    /**
     * @param string $email
     * @param string $newPassword
     * @param string $name
     * @param string|null $subject
     * @param bool $isCreation
     *
     * @return int
     *
     * @throws \Twig\Error\Error
     */
    public function sendMail($email, $subject = null, $pathConfirmation = "localhost", $tokenLifeTime = null)
    {
        $mailer = $this->mailer;
        $fromEmail = $this->container->getParameter('fos_user.registration.confirmation.from_email');
        $body = $this->templating->render('user/user_send_mail_resetting.html.twig', [
            'email' => $email,
            'path_confirmation' => $pathConfirmation,
            'token_life_time' => $tokenLifeTime
        ]);
        $message = new \Swift_Message('Réinitialisation mot de passe');
        $message
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setBody($body, 'text/html')
            ->setTo($email)
            ->setReplyTo($fromEmail);

        return $mailer->send($message);
    }

    /**
     * @param string $email
     * @param string $newPassword
     * @param string $name
     * @param string|null $subject
     * @param bool $isCreation
     *
     * @return int
     *
     * @throws \Twig\Error\Error
     */
    public function sendMailCreation($email, $subject = "Réinitialisation mot de passe", $pathConfirmation = "localhost", $password = null)
    {
        $mailer = $this->mailer;
        $fromEmail = $this->container->getParameter('fos_user.registration.confirmation.from_email');
        $body = $this->templating->render('user/user_send_mail_creation.html.twig', [
            'email' => $email,
            'path_confirmation' => $pathConfirmation,
            'password' => $password
        ]);
        $message = new \Swift_Message($subject);
        $message
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setBody($body, 'text/html')
            ->setTo($email)
            ->setReplyTo($fromEmail);

        return $mailer->send($message);
    }


    /**
     * @param $email
     * @param string $subject
     * @param string $pathConfirmation
     * @param $data
     * @return mixed
     */
    public function sendMailInaugurable($email, $subject = "Projet inaugurable", $body)
    {
        try {
                $mailer = $this->mailer;
                $fromEmail = $this->container->getParameter('fos_user.registration.confirmation.from_email');
                $message = new \Swift_Message($subject);
                $message
                    ->setSubject($subject)
                    ->setFrom($fromEmail)
                    ->setBody($body, 'text/html')
                    ->setTo($email)
                    ->setReplyTo($fromEmail);
                return $mailer->send($message);

        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param $email
     * @param string $subject
     * @param $data
     * @param $body
     * @return mixed
     */
    public function sendMailWithoutParm($email, $subject = "Projet", $body, $mailCreator = null)
    {
        try {
            $mailer = $this->mailer;
            $fromEmail = $this->container->getParameter('fos_user.registration.confirmation.from_email');
            $message = new \Swift_Message($subject);
            $message
                ->setSubject($subject)
                ->setFrom($fromEmail)
                ->setBody($body, 'text/html')
                ->setTo($email)
                ->setReplyTo($fromEmail);
            if ($mailCreator) {
                $message->setTo($mailCreator);
            }
            return $mailer->send($message);

        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}