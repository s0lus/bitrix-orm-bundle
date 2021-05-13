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
     * @param string              $name
     * @param AnnotationInterface $annotation
     *
     * @return ArrayCollection
     */
    public function process(string $name, AnnotationInterface $annotation): ArrayCollection;

    /**
     * @param object $annotation
     *
     * @return boolean
     */
    public function supports($annotation): bool;
}
