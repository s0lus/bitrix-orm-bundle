<?php

namespace Prokl\BitrixOrmBundle\DefinitionFactory;

use Prokl\BitrixOrmBundle\Annotation\AnnotationInterface;
use Prokl\BitrixOrmBundle\Annotation\D7OrmAnnotationInterface;
use Prokl\BitrixOrmBundle\Annotation\OrmAnnotationInterface;
use Prokl\BitrixOrmBundle\Annotation\CatalogGroup;
use Prokl\BitrixOrmBundle\Annotation\D7Item;
use Prokl\BitrixOrmBundle\Annotation\HlbItem;
use Prokl\BitrixOrmBundle\Annotation\HlbReferenceItem;
use Prokl\BitrixOrmBundle\Annotation\IblockElement;
use Prokl\BitrixOrmBundle\Annotation\IblockSection;
use Prokl\BitrixOrmBundle\Base\Factories\CatalogGroupFactory;
use Prokl\BitrixOrmBundle\Base\Factories\D7ItemFactory;
use Prokl\BitrixOrmBundle\Base\Factories\HlbReferenceFactory;
use Prokl\BitrixOrmBundle\Base\Factories\IblockElementFactory;
use Prokl\BitrixOrmBundle\Base\Factories\IblockSectionFactory;
use Prokl\BitrixOrmBundle\Base\Repository\FileRepository;
use Prokl\BitrixOrmBundle\Exception\DefinitionFactory\FactoryDefinitionException;
use ReflectionException;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class FactoryFactory
 * @package Prokl\BitrixOrmBundle\DefinitionFactory
 */
class FactoryFactory
{
    /**
     * @param AnnotationInterface $annotation
     *
     * @return Definition
     * @throws FactoryDefinitionException
     * @throws ReflectionException
     */
    public static function create(AnnotationInterface $annotation): Definition
    {
        $definition = new Definition();
        $definition->setClass(static::getFactoryClass($annotation))
                   ->setArgument('$objectWatcher', new Reference('bitrix_orm.object_watcher'))
                   ->setPublic(false);

        static::setArguments($definition);

        return $definition;
    }

    /**
     * @param AnnotationInterface $annotation
     *
     * @return string
     * @throws FactoryDefinitionException
     */
    protected static function getFactoryClass(AnnotationInterface $annotation): string
    {
        /** @var OrmAnnotationInterface|D7OrmAnnotationInterface $annotation */
        $definedClass = $annotation->getFactoryClass();
        switch (true) {
            case $annotation instanceof IblockElement:
                $requiredClass = IblockElementFactory::class;
                break;
            case $annotation instanceof IblockSection:
                $requiredClass = IblockSectionFactory::class;
                break;
            case $annotation instanceof HlbItem:
            case $annotation instanceof D7Item:
                $requiredClass = D7ItemFactory::class;
                break;
            case $annotation instanceof HlbReferenceItem:
                $requiredClass = HlbReferenceFactory::class;
                if ('' === $definedClass) {
                    $definedClass = $requiredClass;
                }
                break;
            case $annotation instanceof CatalogGroup:
                $requiredClass = CatalogGroupFactory::class;
                if ('' === $definedClass) {
                    $definedClass = $requiredClass;
                }
                break;
            default:
                throw new FactoryDefinitionException(
                    \sprintf('Invalid annotation class %s', \get_class($annotation))
                );
        }

        if (!is_a($definedClass, $requiredClass, true)) {
            throw new FactoryDefinitionException(\sprintf('Invalid factory class "%s"', $definedClass));
        }

        return $definedClass;
    }

    /**
     * @param Definition $definition
     *
     * @throws ReflectionException
     */
    protected static function setArguments(Definition $definition): void
    {
        if ($definition->getClass()) {
            $reflection  = new \ReflectionClass($definition->getClass());
            $constructor = $reflection->getConstructor();

            foreach ($constructor->getParameters() as $parameter) {
                if (!$parameterClass = $parameter->getClass()) {
                    continue;
                }

                $parameterName = '$' . $parameter->getName();
                if (is_a($parameterClass->getName(), FileRepository::class, true)) {
                    $definition->setArgument($parameterName, new Reference('bitrix_orm.file_repository'));
                }
            }
        }
    }
}
