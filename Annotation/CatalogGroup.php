<?php

namespace Prokl\BitrixOrmBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class CatalogGroup
 * @package Prokl\BitrixOrmBundle\Annotation
 *
 * @Annotation
 * @Annotation\Target(value={"CLASS"})
 */
class CatalogGroup implements OrmAnnotationInterface
{
    use OrmAnnotationTrait;
}
