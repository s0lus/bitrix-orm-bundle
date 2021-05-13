<?php

namespace Prokl\BitrixOrmBundle\Driver;

use Prokl\BitrixOrmBundle\Dto\NamespacePrefix;
use Prokl\BitrixOrmBundle\Exception\AnnotationDriver\AnnotationsNotDefinedException;
use Prokl\BitrixOrmBundle\Exception\AnnotationDriver\ClassNotFoundException;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Interface AnnotationDriverInterface
 * @package Prokl\BitrixOrmBundle\Driver
 */
interface AnnotationDriverInterface
{
    /**
     * @param NamespacePrefix $namespacePrefix
     *
     * @return AnnotationDriverInterface
     */
    public function addNamespace(NamespacePrefix $namespacePrefix): AnnotationDriverInterface;

    /**
     * @param string $className
     *
     * @return AnnotationDriverInterface
     * @throws ClassNotFoundException
     */
    public function addAnnotationClass(string $className): AnnotationDriverInterface;

    /**
     * @param string $directoryName
     *
     * @return AnnotationDriverInterface
     */
    public function addDirectoryName(string $directoryName): AnnotationDriverInterface;

    /**
     * @return ArrayCollection
     * @throws AnnotationsNotDefinedException
     */
    public function discover(): ArrayCollection;
}
