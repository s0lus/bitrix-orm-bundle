<?php

namespace Prokl\BitrixOrmBundle\Annotation;

/**
 * Interface IblockAnnotationInterface
 * @package Prokl\BitrixOrmBundle\Annotation
 */
interface IblockAnnotationInterface extends OrmAnnotationInterface
{
    /**
     * @return string
     */
    public function getIblockType(): string;

    /**
     * @return string
     */
    public function getIblockCode(): string;
}
