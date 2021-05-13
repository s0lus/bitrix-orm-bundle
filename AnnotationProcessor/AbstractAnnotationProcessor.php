<?php

namespace Prokl\BitrixOrmBundle\AnnotationProcessor;

use Prokl\BitrixOrmBundle\Annotation\AnnotationInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class AbstractAnnotationProcessor
 * @package Prokl\BitrixOrmBundle\AnnotationProcessor
 */
abstract class AbstractAnnotationProcessor implements AnnotationProcessorInterface
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * AnnotationHelper constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param string              $class
     * @param AnnotationInterface $annotation
     *
     * @return ArrayCollection
     * @throws \ReflectionException
     */
    public function process(string $class, AnnotationInterface $annotation): ArrayCollection
    {
        $this->processParameters($annotation);
        return $this->doProcess($class, $annotation);
    }

    /**
     * @param AnnotationInterface $annotation
     *
     * @throws \ReflectionException
     */
    protected function processParameters(AnnotationInterface $annotation): void
    {
        $reflection = new \ReflectionClass($annotation);

        foreach ($reflection->getProperties() as $property) {
            $value = $property->getValue($annotation);
            if ($this->isContainerParameter($value)) {
                $property->setValue(
                    $annotation,
                    $this->container->getParameter(
                        $this->prepareContainerParameter($value)
                    )
                );
            }
        }
    }

    /**
     * @param mixed $parameter
     *
     * @return boolean
     */
    protected function isContainerParameter($parameter): bool
    {
        return is_string($parameter) && preg_match('~^%.+%$~', $parameter);
    }

    /**
     * @param string $parameter
     *
     * @return string
     */
    protected function prepareContainerParameter(string $parameter): string
    {
        $matches = [];
        preg_match('~^%(.+)%$~', $parameter, $matches);
        return $matches[1];
    }

    /**
     * @param string              $class
     * @param AnnotationInterface $annotation
     *
     * @return ArrayCollection
     */
    abstract protected function doProcess(string $class, AnnotationInterface $annotation): ArrayCollection;
}
