<?php
/**
 * Created by PhpStorm.
 * User: nambinina2
 * Date: 25/09/2019
 * Time: 10:24.
 */

namespace App\Annotation;

use Doctrine\Common\Annotations\AnnotationReader;

class TrackableReader
{
    private $reader;

    /**
     * TrackableReader constructor.
     * @param AnnotationReader $reader
     */
    public function __construct(AnnotationReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param string $entity
     * @return bool
     *
     * @throws \ReflectionException
     */
    public function isTrackable($entity): bool
    {
        $reflection = new \ReflectionClass(get_class($entity));

        return null !== $this->reader->getClassAnnotation($reflection, TrackableClass::class);
    }
}
