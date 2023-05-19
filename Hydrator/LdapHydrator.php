<?php

namespace App\Hydrator;

use FR3D\LdapBundle\Hydrator\AbstractHydrator;
use FR3D\LdapBundle\Model\LdapUserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Populate a FOSUserBundle user with data from LDAP by fixing FR3D hydrator Bug
 * @deprecated 3.0.0
 */
final class LdapHydrator extends AbstractHydrator
{
    /**
     * @var UserManagerInterface
     */
    private $_oUserManager;

    private $ldapAttributes;

    /**
     * LdapHydrator constructor.
     *
     * @param UserManagerInterface $_oUserManager
     * @param array                $_toAttributesMap
     */
    public function __construct(UserManagerInterface $_oUserManager, array $_toAttributesMap)
    {
        parent::__construct($_toAttributesMap);
        $this->_oUserManager = $_oUserManager;
        $this->ldapAttributes = $_toAttributesMap['attributes'];
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(array $ldapEntry): UserInterface
    {
        $username = isset($ldapEntry['samaccountname']) && $ldapEntry['samaccountname'][0] ? $ldapEntry['samaccountname'][0] : null;
        $user = $this->_oUserManager->findUserByUsername($username);
        if (empty($user)) {
            $user = $this->createUser();
        }
        $this->hydrateUserWithAttributesMap($user, $ldapEntry, $this->ldapAttributes);
        if ($user instanceof LdapUserInterface) {
            $user->setDn($ldapEntry['dn']);
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    protected function createUser()
    {
        $oUser = $this->_oUserManager->createUser();
        $oUser->setPassword('');
        if ($oUser instanceof AdvancedUserInterface) {
            $oUser->setEnabled(true);
        }

        return $oUser;
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrateUserWithAttributesMap(UserInterface $_oUser, array $_toLdapUserAttributes, array $_toAttributesMap): void
    {
        foreach ($_toAttributesMap as $oAttr) {
            if (!array_key_exists($oAttr['ldap_attr'], $_toLdapUserAttributes)) {
                continue;
            }
            $oLdapValue = $_toLdapUserAttributes[$oAttr['ldap_attr']];
            if (is_array($oLdapValue) && array_key_exists('count', $oLdapValue)) {
                unset($oLdapValue['count']);
            }
            if (is_array($oLdapValue) && count($oLdapValue) > 0) {
                $oValue = array_shift($oLdapValue);
            } else {
                $oValue = $oLdapValue;
            }
            call_user_func([$_oUser, $oAttr['user_method']], $oValue);
        }
    }
}
