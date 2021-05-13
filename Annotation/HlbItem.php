<?php

namespace Prokl\BitrixOrmBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class HlbItem
 * @package Prokl\BitrixOrmBundle\Annotation
 *
 * @Annotation
 * @Annotation\Target(value={"CLASS"})
 */
class HlbItem implements D7OrmAnnotationInterface
{
    use OrmAnnotationTrait;

    /**
     * @var string
     */
    public $hlBlockName = '';

     /**
     * @var boolean
     */
    public $entityCache = true;
}
