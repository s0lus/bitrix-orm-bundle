<?php

namespace Prokl\BitrixOrmBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class BitrixCache
 * @package Prokl\BitrixOrmBundle\Annotation
 *
 * @Annotation
 * @Annotation\Target(value={"CLASS"})
 */
class BitrixCache implements CacheAnnotationInterface
{
    use CacheAnnotationTrait;

    /**
     * @var string
     */
    public $class = \Prokl\BitrixOrmBundle\Cache\BitrixCache::class;

    /**
     * @var string
     */
    public $tag;

    /**
     * @var string
     */
    public $collectionTag;

    /**
     * @var int int
     */
    public $cacheTime = 3600;
}
