<?php

namespace Prokl\BitrixOrmBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class Hydrator
 * @package Prokl\BitrixOrmBundle\Annotation
 *
 * @Annotation
 * @Annotation\Target(value={"CLASS"})
 */
class Hydrator implements HydratorAnnotationInterface
{
    /**
     * @var string
     */
    public $class;

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }
}
