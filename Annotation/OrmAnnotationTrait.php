<?php

namespace Prokl\BitrixOrmBundle\Annotation;

/**
 * Trait OrmAnnotationTrait
 * @package Prokl\BitrixOrmBundle\Annotation
 */
trait OrmAnnotationTrait
{
    /**
     * @var string $factory
     */
    public $factory = '';

    /**
     * @var string $repository
     */
    public $repository = '';

    /**
     * @return string
     */
    public function getFactoryClass(): string
    {
        return $this->factory;
    }

    /**
     * @return string
     */
    public function getRepositoryClass(): string
    {
        return $this->repository;
    }
}
