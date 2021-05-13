<?php

namespace Prokl\BitrixOrmBundle\AnnotationProcessor;

use Prokl\BitrixOrmBundle\Annotation\AnnotationInterface;
use Prokl\BitrixOrmBundle\Annotation\IblockAnnotationInterface;
use Prokl\BitrixOrmBundle\DefinitionFactory\FactoryFactory;
use Prokl\BitrixOrmBundle\DefinitionFactory\RepositoryFactory;
use Prokl\BitrixOrmBundle\Dto\Definition\FactoryDefinition;
use Prokl\BitrixOrmBundle\Dto\Definition\AbstractOrmDefinition;
use Prokl\BitrixOrmBundle\Dto\Definition\RepositoryDefinition;
use Prokl\BitrixOrmBundle\Helper\ServiceIdHelper;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class IblockAnnotationProcessor
 * @package Prokl\BitrixOrmBundle\AnnotationProcessor
 */
class IblockAnnotationProcessor extends AbstractAnnotationProcessor
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

        if (!$annotation instanceof IblockAnnotationInterface) {
            throw new \LogicException(
                \sprintf('Expected %s, got %s', IblockAnnotationInterface::class, \get_class($annotation))
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
        return $annotation instanceof IblockAnnotationInterface;
    }

    /**
     * @param string                    $class
     * @param IblockAnnotationInterface $annotation
     * @param string                    $factoryDefinitionId
     *
     * @return AbstractOrmDefinition
     */
    protected function createRepositoryDefinition(
        string $class,
        IblockAnnotationInterface $annotation,
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
     * @param IblockAnnotationInterface $annotation
     *
     * @return AbstractOrmDefinition
     */
    protected function createFactoryDefinition(string $class, IblockAnnotationInterface $annotation): AbstractOrmDefinition
    {
        $id         = ServiceIdHelper::getFactoryServiceId($class);
        $definition = FactoryFactory::create($annotation);

        return (new FactoryDefinition())->setId($id)
                                        ->setDefinition($definition);
    }
}
