<?php

namespace Prokl\BitrixOrmBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class IblockElement
 * @package Prokl\BitrixOrmBundle\Annotation
 *
 * @Annotation
 * @Annotation\Target(value={"CLASS"})
 */
class IblockElement implements IblockAnnotationInterface
{
    use OrmAnnotationTrait;
    use IblockAnnotationTrait;
}
