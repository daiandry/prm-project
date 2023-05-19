<?php
/**
 * Created by PhpStorm.
 * User: nambinina2
 * Date: 17/11/2020
 * Time: 14:28
 */

namespace App\DataPersister;


use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\PrmTaches;
use App\Service\ProjetService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class TacheDataPersister
 * @package App\DataPersister
 */
class TacheDataPersister implements ContextAwareDataPersisterInterface
{
    /**
     * @var ProjetService
     */
    private $projetService;
    /**
     * @var
     */
    private $directoryPhoto;
    /**
     * @var mixed
     */
    private $directoryDoc;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $entityManager, ContainerInterface $container, ProjetService $projetService)
    {
        $this->projetService = $projetService;
        $this->directoryPhoto = $container->getParameter('import_path_photos_tache');
        $this->directoryDoc = $container->getParameter('import_path_doc_tache');
        $this->em = $entityManager;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof PrmTaches;
    }

    public function persist($data, array $context = [])
    {

        $photos = $data->getPhotos();
        foreach ($photos as $photo) {
            $this->uploadFile($photo, $this->directoryPhoto);
        }

        $documents = $data->getDocument();
        foreach ($documents as $document) {
            $this->uploadFile($document, $this->directoryDoc);
        }
        $projet = $data->getProjet();

        if ($projet) {
            if ($data->getDateRealisationPrevu() < $data->getDateRealisationReel()) {

                $projet->setEnRetard(true);

            } else {
                $projet->setEnRetard(true);
            }
            $this->em->persist($projet);
            $this->em->flush();
        }
        $this->em->persist($data);
        $this->em->flush();

    }

    /**
     * @param $data
     * @param array $context
     */
    public function remove($data, array $context = [])
    {
        // TODO: Implement remove() method.
    }

    /**
     * @param $value
     * @param $directory
     */
    private function uploadFile($value, $directory)
    {
        $nom = $this->projetService->nameUpload($value->getNom());
        $value->setNom($nom);
        $this->projetService->uploadFile($nom, $value->getChemin(), $directory);
        $value->setChemin($directory . DIRECTORY_SEPARATOR . $nom);
    }

}