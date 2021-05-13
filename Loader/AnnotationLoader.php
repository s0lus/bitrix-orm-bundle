<?php

namespace Prokl\BitrixOrmBundle\Loader;

use Prokl\BitrixOrmBundle\Driver\AnnotationDriverInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class AnnotationLoader
 * @package Prokl\BitrixOrmBundle\Loader
 */
class AnnotationLoader
{
    /**
     * @var AnnotationDriverInterface
     */
    protected $annotationDriver;

    /**
     * @var ArrayCollection
     */
    protected $entities;

    /**
     * AnnotationLoader constructor.
     *
     * @param AnnotationDriverInterface $annotationDriver
     */
    public function __construct(AnnotationDriverInterface $annotationDriver)
    {
        $this->annotationDriver = $annotationDriver;
    }

    /**
     * @return ArrayCollection
     */
    public function getEntities(): ArrayCollection
    {
        if (null === $this->entities) {
            $this->entities = $this->loadEntities();
        }

        return $this->entities;
    }

    /**
     * @return ArrayCollection
     */
    protected function loadEntities(): ArrayCollection
    {
        return $this->annotationDriver->discover();
    }
}
