<?php

namespace Prokl\BitrixOrmBundle\Annotation;

/**
 * Interface OrmAnnotationInterface
 * @package Prokl\BitrixOrmBundle\Annotation\Orm
 */
interface OrmAnnotationInterface extends AnnotationInterface
{
    /**
     * @return string
     */
    public function getFactoryClass(): string;

    /**
     * @return string
     */
    public function getRepositoryClass(): string;
}
