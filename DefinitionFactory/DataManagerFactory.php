<?php

namespace Prokl\BitrixOrmBundle\DefinitionFactory;

use Prokl\BitrixOrmBundle\Annotation\D7Item;
use Prokl\BitrixOrmBundle\Annotation\D7OrmAnnotationInterface;
use Prokl\BitrixOrmBundle\Annotation\HlbItem;
use Prokl\BitrixOrmBundle\Annotation\HlbReferenceItem;
use Prokl\BitrixOrmBundle\Exception\DefinitionFactory\RepositoryDefinitionException;
use Prokl\BitrixOrmBundle\Factory\HlblockFactory;
use Bitrix\Main\ORM\Data\DataManager;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Class DataManagerFactory
 * @package Prokl\BitrixOrmBundle\DefinitionFactory
 */
class DataManagerFactory
{
    /**
     * @param D7OrmAnnotationInterface $annotation
     *
     * @return Definition
     * @throws RepositoryDefinitionException
     */
    public static function create(D7OrmAnnotationInterface $annotation): Definition
    {
        $definition = new Definition();
        $definition->setPublic(false);

        switch (true) {
            case $annotation instanceof D7Item:
                if ('' === $annotation->table) {
                    throw new RepositoryDefinitionException('Table name not defined');
                }

                if (!is_a($annotation->table, DataManager::class, true)) {
                    throw new RepositoryDefinitionException(
                        \sprintf('Class "%s" must extend "%s"', $annotation->table, DataManager::class)
                    );
                }

                $definition->setClass($annotation->table);
                break;
            case $annotation instanceof HlbItem:
            case $annotation instanceof HlbReferenceItem:
                if ('' === $annotation->hlBlockName) {
                    throw new RepositoryDefinitionException('Hlblock name not defined');
                }

                $definition->setArgument('$hlBlockName', $annotation->hlBlockName)
                           ->setFactory(
                               [
                                   HlblockFactory::class,
                                   $annotation->entityCache ? 'createTableObjectWithCache' : 'createTableObject',
                               ]
                           );
                $definition->setClass($annotation->hlBlockName . 'Table');
                break;
            default:
                throw new RepositoryDefinitionException(
                    \sprintf('Unexpected annotation class %s', \get_class($annotation))
                );
        }

        return $definition;
    }
}
