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
use App\Entity\PrmTaches;
use App\Entity\User;
use App\Repository\PrmTachesRepository;
use App\Repository\UserRepository;

final class TacheItemDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    private $tachRep;
    private $itemDataProvider;
    public function __construct(PrmTachesRepository $userRepository, ItemDataProviderInterface $itemDataProvider)
    {
        $this->tachRep = $userRepository;
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
        $user = $this->tachRep->find($id);
        if ($operationName !== "get" || $resourceClass !== PrmTaches::class) {
            return $user;
        }
        $context['skip_null_values'] = false;

        if ($user instanceof PrmTaches) {
            $photos = $user->getPhotos();
            if ($photos) {
                foreach ($photos as $photo) {
                    $this->encodeFile($photo);
                }
            }
            $documents = $user->getDocument();
            if ($photos) {
                foreach ($documents as $document) {
                    $this->encodeFile($document);
                }
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
        return $resourceClass === PrmTaches::class;
    }

    private function encodeFile($value)
    {
        $file = @file_get_contents($value->getChemin());
        $base64 = base64_encode($file);
        $value->setChemin($base64);
    }

}