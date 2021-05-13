<?php

namespace Prokl\BitrixOrmBundle\Annotation;

/**
 * Trait CacheAnnotationTrait
 * @package Prokl\BitrixOrmBundle\Annotation
 */
trait CacheAnnotationTrait
{
    /**
     * @var array
     */
    public $excludedMethods = [];

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @return array
     */
    public function getExcludedMethods(): array
    {
        return $this->excludedMethods;
    }
}
