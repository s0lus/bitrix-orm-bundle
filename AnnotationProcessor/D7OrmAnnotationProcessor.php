<?php

namespace Prokl\BitrixOrmBundle\AnnotationProcessor;

use Prokl\BitrixOrmBundle\Annotation\AnnotationInterface;
use Prokl\BitrixOrmBundle\Annotation\D7OrmAnnotationInterface;
use Prokl\BitrixOrmBundle\DefinitionFactory\DataManagerFactory;
use Prokl\BitrixOrmBundle\DefinitionFactory\FactoryFactory;
use Prokl\BitrixOrmBundle\DefinitionFactory\RepositoryFactory;
use Prokl\BitrixOrmBundle\Dto\Definition\DataManagerDefinition;
use Prokl\BitrixOrmBundle\Dto\Definition\FactoryDefinition;
use Prokl\BitrixOrmBundle\Dto\Definition\AbstractOrmDefinition;
use Prokl\BitrixOrmBundle\Dto\Definition\RepositoryDefinition;
use Prokl\BitrixOrmBundle\Helper\ServiceIdHelper;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class D7OrmAnnotationProcessor
 * @package Prokl\BitrixOrmBundle\AnnotationProcessor
 */
class D7OrmAnnotationProcessor extends AbstractAnnotationProcessor
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

        /** @var D7OrmAnnotationInterface $annotation */
        $factoryDefinition     = $this->createFactoryDefinition($class, $annotation);
        $dataManagerDefinition = $this->createDataManagerDefinition($class, $annotation);
        $repositoryDefinition  = $this->createRepositoryDefinition(
            $class,
            $annotation,
            $factoryDefinition->getId(),
            $dataManagerDefinition->getId()
        );

        $result->add($factoryDefinition);
        $result->add($dataManagerDefinition);
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
        return $annotation instanceof D7OrmAnnotationInterface;
    }

    /**
     * @param string                   $class
     * @param D7OrmAnnotationInterface $annotation
     *
     * @return AbstractOrmDefinition
     */
    protected function createFactoryDefinition(string $class, D7OrmAnnotationInterface $annotation): AbstractOrmDefinition
    {

        $id         = ServiceIdHelper::getFactoryServiceId($class);
        $definition = FactoryFactory::create($annotation);

        return (new FactoryDefinition())->setId($id)
                                        ->setDefinition($definition);
    }

    /**
     * @param string                   $class
     * @param D7OrmAnnotationInterface $annotation
     * @param string                   $factoryDefinitionId
     * @param string                   $dataManagerDefinitionId
     *
     * @return AbstractOrmDefinition
     */
    protected function createRepositoryDefinition(
        string $class,
        D7OrmAnnotationInterface $annotation,
        string $factoryDefinitionId,
        string $dataManagerDefinitionId
    ): AbstractOrmDefinition
    {
        $id         = ServiceIdHelper::getRepositoryServiceId($class);
        $definition = RepositoryFactory::createD7($annotation, $factoryDefinitionId, $dataManagerDefinitionId);

        return (new RepositoryDefinition())->setId($id)
                                           ->setDefinition($definition);
    }

    /**
     * @param string                   $class
     * @param D7OrmAnnotationInterface $annotation
     *
     * @return AbstractOrmDefinition
     */
    protected function createDataManagerDefinition(string $class, D7OrmAnnotationInterface $annotation): AbstractOrmDefinition
    {
        $id         = ServiceIdHelper::getDataManagerServiceId($class);
        $definition = DataManagerFactory::create($annotation);

        return (new DataManagerDefinition())->setId($id)
                                            ->setDefinition($definition);
    }
}
