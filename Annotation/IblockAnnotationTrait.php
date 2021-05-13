<?php

namespace Prokl\BitrixOrmBundle\Annotation;

/**
 * Trait IblockAnnotationTrait
 * @package Prokl\BitrixOrmBundle\Annotation
 */
trait IblockAnnotationTrait
{
    /**
     * @var string
     */
    public $iblockType = '';

    /**
     * @var string
     */
    public $iblockCode = '';

    /**
     * @return string
     */
    public function getIblockType(): string
    {
        return $this->iblockType;
    }

    /**
     * @return string
     */
    public function getIblockCode(): string
    {
        return $this->iblockCode;
    }
}
