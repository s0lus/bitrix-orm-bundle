<?php

namespace Prokl\BitrixOrmBundle\AnnotationProcessor;

use Prokl\BitrixOrmBundle\Annotation\AnnotationInterface;
use Prokl\BitrixOrmBundle\Annotation\CacheAnnotationInterface;
use Prokl\BitrixOrmBundle\Annotation\BitrixCache;
use Prokl\BitrixOrmBundle\DefinitionFactory\CacheFactory;
use Prokl\BitrixOrmBundle\Dto\Definition\ArrayCacheDefinition;
use Prokl\BitrixOrmBundle\Dto\Definition\BitrixCacheDefinition;
use Prokl\BitrixOrmBundle\Helper\ServiceIdHelper;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class CacheAnnotationProcessor
 * @package Prokl\BitrixOrmBundle\AnnotationProcessor
 */
class CacheAnnotationProcessor extends AbstractAnnotationProcessor
{
    /**
     * @param string              $class
     * @param AnnotationInterface $annotation
     *
     * @return ArrayCollection
     * @throws \ReflectionException
     */
    protected function doProcess(string $class, AnnotationInterface $annotation): ArrayCollection
    {
        $result = new ArrayCollection();

        /** @var CacheAnnotationInterface $annotation */
        $definition = CacheFactory::create($annotation);

        if ($annotation instanceof BitrixCache) {
            $id         = ServiceIdHelper::getBitrixCacheServiceId($class);
            $item = new BitrixCacheDefinition();
            $definition->setArgument('$modelClass', $class);
        } else {
            $id         = ServiceIdHelper::getCacheServiceId($class);
            $item = new ArrayCacheDefinition();
        }
        $result->add(
            $item->setExcludedMethods($annotation->getExcludedMethods())
                 ->setId($id)
                 ->setDefinition($definition)
        );

        return $result;
    }

    /**
     * @param object $annotation
     *
     * @return boolean
     */
    public function supports($annotation): bool
    {
        return $annotation instanceof CacheAnnotationInterface;
    }
}
