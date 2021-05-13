<?php

namespace Prokl\BitrixOrmBundle\Dto;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class AnnotationDiscoveryResult
 * @package Prokl\BitrixOrmBundle\Dto
 */
class AnnotationDiscoveryResult
{
    /**
     * @var string
     */
    protected $class;

    /**
     * @var ArrayCollection
     */
    protected $classAnnotations;

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @param string $class
     *
     * @return AnnotationDiscoveryResult
     */
    public function setClass(string $class): AnnotationDiscoveryResult
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getClassAnnotations(): ArrayCollection
    {
        return $this->classAnnotations;
    }

    /**
     * @param ArrayCollection $classAnnotations
     *
     * @return AnnotationDiscoveryResult
     */
    public function setClassAnnotations(ArrayCollection $classAnnotations): AnnotationDiscoveryResult
    {
        $this->classAnnotations = $classAnnotations;

        return $this;
    }
}
