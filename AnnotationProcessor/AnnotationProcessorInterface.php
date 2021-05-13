<?php

namespace Prokl\BitrixOrmBundle\AnnotationProcessor;

use Prokl\BitrixOrmBundle\Annotation\AnnotationInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Interface AnnotationProcessorInterface
 * @package Prokl\BitrixOrmBundle\AnnotationProcessor
 */
interface AnnotationProcessorInterface
{
    /**
     * @param string              $class
     * @param AnnotationInterface $annotation
     *
     * @return ArrayCollection
     */
    public function process(string $class, AnnotationInterface $annotation): ArrayCollection;

    /**
     * @param object $annotation
     *
     * @return boolean
     */
    public function supports($annotation): bool;
}
