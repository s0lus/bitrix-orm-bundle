<?php

namespace Prokl\BitrixOrmBundle\DefinitionFactory;

use Prokl\BitrixOrmBundle\Annotation\BitrixCache;
use Prokl\BitrixOrmBundle\Annotation\CacheAnnotationInterface;
use Prokl\BitrixOrmBundle\Cache\BitrixCacheInterface;
use Prokl\BitrixOrmBundle\Cache\CacheInterface;
use Prokl\BitrixOrmBundle\Exception\DefinitionFactory\CacheDefinitionException;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class CacheFactory
 * @package Prokl\BitrixOrmBundle\DefinitionFactory
 */
class CacheFactory
{
    /**
     * @param CacheAnnotationInterface $annotation
     *
     * @return Definition
     * @throws \ReflectionException
     */
    public static function create(CacheAnnotationInterface $annotation): Definition
    {
        if ($annotation instanceof BitrixCache) {
            $definition = static::createBitrixCacheDefinition($annotation);
        } else {
            $definition = static::createCacheDefinition($annotation);
        }

        return $definition;
    }

    /**
     * @param BitrixCache $annotation
     *
     * @return Definition
     * @throws \ReflectionException
     */
    protected static function createBitrixCacheDefinition(BitrixCache $annotation): Definition
    {
        $reflection = new \ReflectionClass($annotation->getClass());
        if (!$reflection->implementsInterface(BitrixCacheInterface::class)) {
            throw new CacheDefinitionException(
                \sprintf('%s must implement %s', $annotation->getClass(), CacheInterface::class)
            );
        }
        $definition = static::createCacheDefinition($annotation);

        $definition->setArgument('$cacheTime', $annotation->cacheTime)
                   ->setArgument('$tag', $annotation->tag)
                   ->setArgument('$collectionTag', $annotation->collectionTag)
                   ->addMethodCall('setFileRepository', [new Reference('bitrix_orm.file_repository')]);

        return $definition;
    }

    /**
     * @param CacheAnnotationInterface $annotation
     *
     * @return Definition
     * @throws \ReflectionException
     */
    protected static function createCacheDefinition(CacheAnnotationInterface $annotation): Definition
    {
        $reflection = new \ReflectionClass($annotation->getClass());
        if (!$reflection->implementsInterface(CacheInterface::class)) {
            throw new CacheDefinitionException(
                \sprintf('%s must implement %s', $annotation->getClass(), CacheInterface::class)
            );
        }

        $definition = new Definition();
        $definition->setClass($annotation->getClass());

        return $definition;
    }
}
