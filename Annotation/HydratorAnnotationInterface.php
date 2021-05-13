<?php

namespace Prokl\BitrixOrmBundle\Annotation;

/**
 * Interface HydratorAnnotationInterface
 * @package Prokl\BitrixOrmBundle\Annotation
 */
interface HydratorAnnotationInterface extends AnnotationInterface
{
    /**
     * @return string
     */
    public function getClass(): string;
}
