<?php

namespace App\Provider;

use FR3D\LdapBundle\Ldap\LdapManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use FR3D\LdapBundle\Model\LdapUserInterface;
use Symfony\Component\Security\Core\Exception\DisabledException;

/**
 * Provides users from Ldap and database.
 * @author Victor RAZAFIMIANDRISOA
 */
class LdapUserProvider implements UserProviderInterface
{
    /**
     * @var LdapManagerInterface
     */
    protected $ldapManager;

    /**
     * @var UserManagerInterface
     */
    private $oUserManager;

    /**
     * @var null|LoggerInterface
     **/
    protected $logger;

    /**
     * List of users no need ldap
     *
     * @var array<string>
     **/
    protected $usersFos;


    /**
     * LdapUserProvider constructor.
     *
     * @param LdapManagerInterface $ldapManager
     * @param UserManagerInterface $oUserManager
     * @param $usersFos
     * @param LoggerInterface|null $logger
     */
    public function __construct(LdapManagerInterface $ldapManager, UserManagerInterface $oUserManager, $usersFos, LoggerInterface $logger = null)
    {
        $this->ldapManager = $ldapManager;
        $this->oUserManager = $oUserManager;
        $this->logger = $logger;
        $this->usersFos = $usersFos;
    }

    /**
     * @param string $username
     *
     * @return null|UserInterface
     */
    public function loadUserByUsername($username)
    {
        $preUserCheck = $this->oUserManager->findUserByUsername($username);
        if (empty($preUserCheck)) {
            $this->logInfo(
                'User {username} {result} on Database check', [
                                                                'action'   => 'loadUserByUsername',
                                                                'username' => $username,
                                                                'result'   => 'not found',
                                                            ]
            );
            $resultEx = new UsernameNotFoundException(sprintf('User "%s" not found', $username));
            $resultEx->setUsername($username);
            throw $resultEx;
        }

        //dump($preUserCheck);
        if ($preUserCheck instanceof LdapUserInterface && !$preUserCheck->isEnabled()) {
            $this->logInfo(
                'User {username} {result} on Database check status', [
                                                                       'action'   => 'loadUserByUsername',
                                                                       'username' => $username,
                                                                       'result'   => 'User account is disabled.',
                                                                   ]
            );
            $ex = new DisabledException('User account is disabled.');
            $ex->setUser($preUserCheck);
            throw $ex;
        }
        if (in_array($preUserCheck->getUsername(), $this->usersFos)) {
            $preUserCheck->setFosAdminUsers($this->usersFos);
            return $preUserCheck;
        }


        $user = $this->ldapManager->findUserByUsername($username);


        if (empty($user)) {
            $this->logInfo(
                'User {username} {result} on LDAP', [
                                                      'action'   => 'loadUserByUsername',
                                                      'username' => $username,
                                                      'result'   => 'not found',
                                                  ]
            );
            $ex = new UsernameNotFoundException(sprintf('User "%s" not found', $username));
            $ex->setUsername($username);
            throw $ex;
        }
        $this->logInfo(
            'User {username} {result} on LDAP', [
                                                  'action'   => 'loadUserByUsername',
                                                  'username' => $username,
                                                  'result'   => 'found',
                                              ]
        );
        //dump($user);die('---------1--------');
        //die('eto 2');
        return $user;
    }

    /**
     * @param UserInterface $user
     *
     * @return null|UserInterface
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return true;
    }

    /**
     * Log a message into the logger if this exists.
     *
     * @param string $message
     * @param array  $context
     */
    private function logInfo($message, array $context = [])
    {
        if (!$this->logger) {
            return;
        }
        $this->logger->info($message, $context);
    }
}

