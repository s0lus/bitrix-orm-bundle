<?php

namespace Prokl\BitrixOrmBundle\Annotation;

use Prokl\BitrixOrmBundle\Cache\ArrayCache;
use Doctrine\Common\Annotations\Annotation;

/**
 * Class Cache
 * @package Prokl\BitrixOrmBundle\Annotation
 *
 * @Annotation
 * @Annotation\Target(value={"CLASS"})
 */
class Cache implements CacheAnnotationInterface
{
    use CacheAnnotationTrait;

    /**
     * @var string
     */
    public $class = ArrayCache::class;
}
