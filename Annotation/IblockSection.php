<?php

namespace Prokl\BitrixOrmBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class IblockSection
 * @package Prokl\BitrixOrmBundle\Annotation
 *
 * @Annotation
 * @Annotation\Target(value={"CLASS"})
 */
class IblockSection implements IblockAnnotationInterface
{
    use OrmAnnotationTrait;
    use IblockAnnotationTrait;
}
