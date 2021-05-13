<?php

namespace Prokl\BitrixOrmBundle\AnnotationProcessor;

use Prokl\BitrixOrmBundle\Annotation\AnnotationInterface;
use Prokl\BitrixOrmBundle\Annotation\CatalogGroup;
use Prokl\BitrixOrmBundle\DefinitionFactory\FactoryFactory;
use Prokl\BitrixOrmBundle\DefinitionFactory\RepositoryFactory;
use Prokl\BitrixOrmBundle\Dto\Definition\FactoryDefinition;
use Prokl\BitrixOrmBundle\Dto\Definition\AbstractOrmDefinition;
use Prokl\BitrixOrmBundle\Dto\Definition\RepositoryDefinition;
use Prokl\BitrixOrmBundle\Helper\ServiceIdHelper;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class CatalogGroupAnnotationProcessor
 * @package Prokl\BitrixOrmBundle\AnnotationProcessor
 */
class CatalogGroupAnnotationProcessor extends AbstractAnnotationProcessor
{
    /**
     * @param string              $class
     * @param AnnotationInterface $annotation
     *
     * @return ArrayCollection
     */
    protected function doProcess(string $class, AnnotationInterface $annotation): ArrayCollection
    {
        $result = new ArrayCollection();

        if (!$annotation instanceof CatalogGroup) {
            throw new \LogicException(
                \sprintf('Expected %s, got %s', CatalogGroup::class, \get_class($annotation))
            );
        }
        $factoryDefinition    = $this->createFactoryDefinition($class, $annotation);
        $repositoryDefinition = $this->createRepositoryDefinition($class, $annotation, $factoryDefinition->getId());

        $result->add($factoryDefinition);
        $result->add($repositoryDefinition);

        return $result;
    }

    /**
     * @param object $annotation
     *
     * @return boolean
     */
    public function supports($annotation): bool
    {
        return $annotation instanceof CatalogGroup;
    }

    /**
     * @param string                    $class
     * @param CatalogGroup $annotation
     * @param string                    $factoryDefinitionId
     *
     * @return AbstractOrmDefinition
     */
    protected function createRepositoryDefinition(
        string $class,
        CatalogGroup $annotation,
        string $factoryDefinitionId
    ): AbstractOrmDefinition
    {
        $id         = ServiceIdHelper::getRepositoryServiceId($class);
        $definition = RepositoryFactory::create($annotation, $factoryDefinitionId);

        return (new RepositoryDefinition())->setId($id)
                                           ->setDefinition($definition);
    }

    /**
     * @param string                    $class
     * @param CatalogGroup $annotation
     *
     * @return AbstractOrmDefinition
     */
    protected function createFactoryDefinition(string $class, CatalogGroup $annotation): AbstractOrmDefinition
    {
        $id         = ServiceIdHelper::getFactoryServiceId($class);
        $definition = FactoryFactory::create($annotation);

        return (new FactoryDefinition())->setId($id)
                                        ->setDefinition($definition);
    }
}
