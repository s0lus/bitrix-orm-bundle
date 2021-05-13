<?php

namespace Prokl\BitrixOrmBundle\DefinitionFactory;

use Prokl\BitrixOrmBundle\Base\Repository\CatalogGroupRepository;
use Prokl\BitrixOrmBundle\Base\Repository\D7Repository;
use Prokl\BitrixOrmBundle\Base\Repository\HlbReferenceRepository;
use Prokl\BitrixOrmBundle\Base\Repository\IblockElementRepository;
use Prokl\BitrixOrmBundle\Base\Repository\IblockSectionRepository;
use Prokl\BitrixOrmBundle\Tools\IblockTools;
use Prokl\BitrixOrmBundle\Annotation\AnnotationInterface;
use Prokl\BitrixOrmBundle\Annotation\CatalogGroup;
use Prokl\BitrixOrmBundle\Annotation\D7Item;
use Prokl\BitrixOrmBundle\Annotation\D7OrmAnnotationInterface;
use Prokl\BitrixOrmBundle\Annotation\HlbItem;
use Prokl\BitrixOrmBundle\Annotation\HlbReferenceItem;
use Prokl\BitrixOrmBundle\Annotation\IblockElement;
use Prokl\BitrixOrmBundle\Annotation\IblockSection;
use Prokl\BitrixOrmBundle\Annotation\OrmAnnotationInterface;
use Prokl\BitrixOrmBundle\Exception\DefinitionFactory\RepositoryDefinitionException;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class RepositoryFactory
 * @package Prokl\BitrixOrmBundle\DefinitionFactory
 */
class RepositoryFactory
{
    /**
     * @param OrmAnnotationInterface $annotation
     * @param string $factoryDefinitionId
     *
     * @return Definition
     * @throws RepositoryDefinitionException
     */
    public static function create(
        OrmAnnotationInterface $annotation,
        string $factoryDefinitionId
    ): Definition {

        $definition = new Definition();
        $definition->setClass(static::getRepositoryClass($annotation))
                   ->setArgument('$factory', new Reference($factoryDefinitionId))
                   ->setPublic(true);

        static::setArguments($annotation, $definition);

        return $definition;
    }

    /**
     * @param AnnotationInterface $annotation
     * @param string $factoryDefinitionId
     * @param string $dataManagerId
     *
     * @return Definition
     * @throws RepositoryDefinitionException
     */
    public static function createD7(
        AnnotationInterface $annotation,
        string $factoryDefinitionId,
        string $dataManagerId
    ): Definition {
        $definition = new Definition();
        $definition->setClass(static::getRepositoryClass($annotation))
                   ->setArgument('$dataManager', new Reference($dataManagerId))
                   ->setArgument('$factory', new Reference($factoryDefinitionId))
                   ->setPublic(true);

        return $definition;
    }

    /**
     * @param AnnotationInterface $annotation
     *
     * @return string
     * @throws RepositoryDefinitionException
     */
    protected static function getRepositoryClass(AnnotationInterface $annotation): string
    {
        /** @var OrmAnnotationInterface|D7OrmAnnotationInterface $annotation */
        $definedClass = $annotation->getRepositoryClass();
        switch (true) {
            case $annotation instanceof IblockElement:
                $requiredClass = IblockElementRepository::class;
                break;
            case $annotation instanceof IblockSection:
                $requiredClass = IblockSectionRepository::class;
                break;
            case $annotation instanceof HlbItem:
            case $annotation instanceof D7Item:
                $requiredClass = D7Repository::class;
                break;
            case $annotation instanceof HlbReferenceItem:
                $requiredClass = HlbReferenceRepository::class;
                break;
            case $annotation instanceof CatalogGroup:
                $requiredClass = CatalogGroupRepository::class;
                break;
            default:
                throw new RepositoryDefinitionException(
                    \sprintf('Invalid annotation class %s', \get_class($annotation))
                );
        }

        $repositoryClass = $definedClass ?: $requiredClass;

        if (!is_a($repositoryClass, $requiredClass, true)) {
            throw new RepositoryDefinitionException(\sprintf('Invalid repository class "%s"', $definedClass));
        }

        return $repositoryClass;
    }

    /**
     * @param AnnotationInterface $annotation
     * @param Definition          $definition
     *
     * @throws RepositoryDefinitionException
     */
    protected static function setArguments(AnnotationInterface $annotation, Definition $definition): void
    {
        switch (true) {
            case $annotation instanceof IblockElement:
            case $annotation instanceof IblockSection:
                $definition->setArgument(
                    '$iblockId',
                    static::getIblockId($annotation->iblockType, $annotation->iblockCode)
                );
                break;
        }
    }

    /**
     * @param string $iblockType Тип инфоблока.
     * @param string $iblockCode Код инфоблока.
     *
     * @return integer
     * @throws RepositoryDefinitionException
     */
    protected static function getIblockId(string $iblockType, string $iblockCode): int
    {
        try {
            return IblockTools::getIblockId($iblockType, $iblockCode);
        } catch (\Exception $e) {
            throw new RepositoryDefinitionException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }
}
