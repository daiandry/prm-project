<?php
/**
 * Created by PhpStorm.
 * User: nambinina2
 * Date: 06/11/2020
 * Time: 16:01
 */

namespace App\Utils;


use Symfony\Component\Security\Core\User\UserInterface;

class Fonctions
{
    /**
     * @param int $size
     *
     * @return bool|string
     */
    public static function generatePassword($size = 8)
    {
        $p = openssl_random_pseudo_bytes(ceil($size * 0.67), $crypto_strong);
        $p = str_replace('=', '', base64_encode($p));
        $p = strtr($p, '+/', '^*');

        return substr($p, 0, $size);
    }

    public static function checkCredential(UserInterface $user, $credential)
    {
        if ($credential === $user->getPassword()) {
            return true;
        } else {
            return false;
        }
    }

}