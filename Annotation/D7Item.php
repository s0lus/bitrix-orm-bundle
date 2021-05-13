<?php

namespace Prokl\BitrixOrmBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class D7Item
 * @package Prokl\BitrixOrmBundle\Annotation
 *
 * @Annotation
 * @Annotation\Target(value={"CLASS"})
 */
class D7Item implements D7OrmAnnotationInterface
{
    use OrmAnnotationTrait;

    /**
     * @var string
     */
    public $table = '';
}
