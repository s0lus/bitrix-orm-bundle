<?php

namespace Prokl\BitrixOrmBundle\Registry;

use Prokl\BitrixOrmBundle\Annotation\AnnotationInterface;
use Prokl\BitrixOrmBundle\AnnotationProcessor\AnnotationProcessorInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class AnnotationProcessorRegistry
 * @package Prokl\BitrixOrmBundle\Registry
 */
class AnnotationProcessorRegistry
{
    /**
     * @var ArrayCollection
     */
    protected $processors;

    /**
     * AnnotationProcessorRegistry constructor.
     */
    public function __construct()
    {
        $this->processors = new ArrayCollection();
    }

    /**
     * @param AnnotationInterface $annotation
     *
     * @return AnnotationProcessorInterface
     */
    public function get(AnnotationInterface $annotation): AnnotationProcessorInterface
    {
        $result = null;

        /** @var AnnotationProcessorInterface $processor */
        foreach ($this->processors as $processor) {
            if ($processor->supports($annotation)) {
                $result = $processor;
            }
        }

        if (null === $result) {
            throw new \LogicException(
                \sprintf('No processors found for "%s" annotation', \get_class($annotation))
            );
        }

        return $result;
    }

    /**
     * @param AnnotationProcessorInterface $processor
     *
     * @return AnnotationProcessorRegistry
     */
    public function set(AnnotationProcessorInterface $processor): AnnotationProcessorRegistry
    {
        $this->processors->add($processor);

        return $this;
    }
}
