<?php
/**
 * Created by PhpStorm.
 * User: nambinina2
 * Date: 10/11/2020
 * Time: 11:25
 */

namespace App\DataProvider;


use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\User;
use App\Repository\UserRepository;

final class UserItemDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    private $userRep;
    private $itemDataProvider;
    public function __construct(UserRepository $userRepository, ItemDataProviderInterface $itemDataProvider)
    {
        $this->userRep = $userRepository;
        $this->itemDataProvider = $itemDataProvider;
    }

    /**
     * @param string $resourceClass
     * @param array|int|string $id
     * @param string|null $operationName
     * @param array $context
     * @return User|null|object|void
     */
    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        if ($operationName !== "get" || $resourceClass !== User::class) {

            return $user = $this->userRep->find($id);;
        }
        $context['skip_null_values'] = false;
        $user = $this->userRep->find($id);
        if ($user instanceof User) {
            $photo = $user->getPhoto();
            if ($photo) {
                $file = @file_get_contents($photo->getChemin());
                $base64 = base64_encode($file);
                $photo->setChemin($base64);
            }
        }
        return $user;
    }

    /**
     * @param string $resourceClass
     * @param string|null $operationName
     * @param array $context
     * @return bool
     */
    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === User::class;
    }

}