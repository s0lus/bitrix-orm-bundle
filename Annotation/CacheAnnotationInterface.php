<?php

namespace Prokl\BitrixOrmBundle\Annotation;

/**
 * Interface CacheAnnotationInterface
 * @package Prokl\BitrixOrmBundle\Annotation
 */
interface CacheAnnotationInterface extends AnnotationInterface
{
    /**
     * @return string
     */
    public function getClass(): string;

    /**
     * @return array
     */
    public function getExcludedMethods(): array;
}
