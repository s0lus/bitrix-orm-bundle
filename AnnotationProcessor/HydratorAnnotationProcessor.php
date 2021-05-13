<?php

namespace Prokl\BitrixOrmBundle\AnnotationProcessor;

use Prokl\BitrixOrmBundle\Annotation\AnnotationInterface;
use Prokl\BitrixOrmBundle\Annotation\HydratorAnnotationInterface;
use Prokl\BitrixOrmBundle\DefinitionFactory\HydratorFactory;
use Prokl\BitrixOrmBundle\Dto\Definition\HydratorDefinition;
use Prokl\BitrixOrmBundle\Helper\ServiceIdHelper;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class HydratorAnnotationProcessor
 * @package Prokl\BitrixOrmBundle\AnnotationProcessor
 */
class HydratorAnnotationProcessor extends AbstractAnnotationProcessor
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

        /** @var HydratorAnnotationInterface $annotation */
        $id         = ServiceIdHelper::getHydratorServiceId($class);
        $definition = HydratorFactory::create($annotation);

        $result->add(
            (new HydratorDefinition())->setId($id)
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
        return $annotation instanceof HydratorAnnotationInterface;
    }
}
