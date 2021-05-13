<?php

namespace Prokl\BitrixOrmBundle\DefinitionFactory;

use Prokl\BitrixOrmBundle\Annotation\HydratorAnnotationInterface;
use Prokl\BitrixOrmBundle\Exception\DefinitionFactory\HydratorDefinitionException;
use Prokl\BitrixOrmBundle\Hydrator\HydratorInterface;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Class HydratorFactory
 * @package Prokl\BitrixOrmBundle\DefinitionFactory
 */
class HydratorFactory
{
    /**
     * @param HydratorAnnotationInterface $annotation
     *
     * @return Definition
     * @throws \ReflectionException
     */
    public static function create(HydratorAnnotationInterface $annotation): Definition
    {
        $reflection = new \ReflectionClass($annotation->getClass());
        if (!$reflection->implementsInterface(HydratorInterface::class)) {
            throw new HydratorDefinitionException(
                \sprintf('%s must implement %s', $annotation->getClass(), HydratorInterface::class)
            );
        }

        $definition = new Definition();
        $definition->setClass($annotation->getClass())
                   ->setAutowired(true);

        return $definition;
    }
}
