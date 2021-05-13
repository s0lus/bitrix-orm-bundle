<?php

namespace Prokl\BitrixOrmBundle\DependencyInjection\Compiler;

use Prokl\BitrixOrmBundle\Annotation\AnnotationInterface;
use Prokl\BitrixOrmBundle\Annotation\Cache;
use Prokl\BitrixOrmBundle\Annotation\Hydrator;
use Prokl\BitrixOrmBundle\Annotation\CatalogGroup;
use Prokl\BitrixOrmBundle\Annotation\D7Item;
use Prokl\BitrixOrmBundle\Annotation\HlbItem;
use Prokl\BitrixOrmBundle\Annotation\HlbReferenceItem;
use Prokl\BitrixOrmBundle\Annotation\IblockElement;
use Prokl\BitrixOrmBundle\Annotation\IblockSection;
use Prokl\BitrixOrmBundle\AnnotationProcessor\CacheAnnotationProcessor;
use Prokl\BitrixOrmBundle\AnnotationProcessor\CatalogGroupAnnotationProcessor;
use Prokl\BitrixOrmBundle\AnnotationProcessor\D7OrmAnnotationProcessor;
use Prokl\BitrixOrmBundle\AnnotationProcessor\HydratorAnnotationProcessor;
use Prokl\BitrixOrmBundle\AnnotationProcessor\IblockAnnotationProcessor;
use Prokl\BitrixOrmBundle\Driver\AnnotationDriver;
use Prokl\BitrixOrmBundle\Driver\AnnotationDriverInterface;
use Prokl\BitrixOrmBundle\Dto\AnnotationDiscoveryResult;
use Prokl\BitrixOrmBundle\Dto\Definition\ArrayCacheDefinition;
use Prokl\BitrixOrmBundle\Dto\Definition\BitrixCacheDefinition;
use Prokl\BitrixOrmBundle\Dto\Definition\DataManagerDefinition;
use Prokl\BitrixOrmBundle\Dto\Definition\FactoryDefinition;
use Prokl\BitrixOrmBundle\Dto\Definition\HydratorDefinition;
use Prokl\BitrixOrmBundle\Dto\Definition\RepositoryDefinition;
use Prokl\BitrixOrmBundle\Dto\NamespacePrefix;
use Prokl\BitrixOrmBundle\Enum\SymfonyParameters;
use Prokl\BitrixOrmBundle\Exception\AnnotationDriver\ClassNotFoundException;
use Prokl\BitrixOrmBundle\Exception\AnnotationNotFoundException;
use Prokl\BitrixOrmBundle\Exception\DefinitionFactory\FactoryDefinitionException;
use Prokl\BitrixOrmBundle\Exception\DefinitionFactory\RepositoryDefinitionException;
use Prokl\BitrixOrmBundle\Exception\NamespacesNotDefinedException;
use Prokl\BitrixOrmBundle\Helper\ServiceIdHelper;
use Prokl\BitrixOrmBundle\Loader\AnnotationLoader;
use Prokl\BitrixOrmBundle\Proxy\CacheProxy;
use Prokl\BitrixOrmBundle\Proxy\HydratorProxy;
use Prokl\BitrixOrmBundle\Registry\AnnotationProcessorRegistry;
use Prokl\BitrixOrmBundle\Registry\RepositoryRegistryInterface;
use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class EntityAnnotationsProcessor
 * @package Prokl\BitrixOrmBundle\DependencyInjection\Compiler
 */
class EntityAnnotationsProcessor implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     *
     * @throws AnnotationException
     * @throws ClassNotFoundException
     */
    public function process(ContainerBuilder $container): void
    {
        $annotationReader = new AnnotationReader();
        $annotationLoader = new AnnotationLoader($this->getAnnotationDriver($annotationReader, $container));
        $processorRegistry = $this->getAnnotationProcessorRegistry($container);

        $discoveryResults = $annotationLoader->getEntities();

        /** @var AnnotationDiscoveryResult $discoveryResult */
        foreach ($discoveryResults as $discoveryResult) {
            try {
                $annotations = $this->getAnnotations($discoveryResult);
            } catch (AnnotationNotFoundException $e) {
                continue;
            }

            $definitions = new ArrayCollection();
            /** @var AnnotationInterface $annotation */
            foreach ($annotations as $annotation) {
                $processor = $processorRegistry->get($annotation);
                foreach ($processor->process($discoveryResult->getClass(), $annotation) as $definition) {
                    $definitions->add($definition);
                }
            }

            $this->defineOrmServices($container, $definitions, $discoveryResult->getClass());
        }
    }

    /**
     * @param AnnotationDiscoveryResult $entityInfo
     *
     * @return ArrayCollection
     */
    protected function getAnnotations(AnnotationDiscoveryResult $entityInfo): ArrayCollection
    {
        $result = new ArrayCollection();
        foreach ($entityInfo->getClassAnnotations() as $classAnnotation) {
            if (!$classAnnotation instanceof AnnotationInterface) {
                continue;
            }

            $result->add($classAnnotation);
        }

        if ($result->isEmpty()) {
            throw new AnnotationNotFoundException(
                \sprintf('No valid annotations found for %s', $entityInfo->getClass())
            );
        }

        return $result;
    }

    /**
     * @param Reader           $reader    Читатель аннотаций.
     * @param ContainerBuilder $container Контейнер.
     *
     * @return AnnotationDriverInterface
     * @throws ClassNotFoundException
     */
    protected function getAnnotationDriver(Reader $reader, ContainerBuilder $container): AnnotationDriverInterface
    {
        $annotationDriver = new AnnotationDriver(
            $reader,
            $this->getSourcesDir($container)
        );

        if ($container->hasParameter(SymfonyParameters::NAMESPACES_PARAMETER_NAME)) {
            /** @var string[] $namespaces */
            $namespaces = $container->getParameter(SymfonyParameters::NAMESPACES_PARAMETER_NAME);

            /**
             * @var string $prefix
             * @var string $dir
             */
            foreach ($namespaces as $prefix => $dir) {
                $annotationDriver->addNamespace(
                    (new NamespacePrefix())->setPrefix($prefix)
                        ->setDir($dir)
                );
            }
        }

        if ($container->hasParameter(SymfonyParameters::NAMESPACES_EXTENDED_PARAMETER_NAME)) {
            $namespaces = (array)$container->getParameter(SymfonyParameters::NAMESPACES_EXTENDED_PARAMETER_NAME);
            /**
             * @var string $prefix
             * @var string $dir
             */
            foreach ($namespaces as $prefix => $dir) {
                $annotationDriver->addNamespace(
                    (new NamespacePrefix())->setPrefix($prefix)
                        ->setDir($dir)
                );
            }
        }

        if (!$annotationDriver->getNamespaces()) {
            throw new NamespacesNotDefinedException(
                \sprintf(
                    'No namespaces defined (use "%s" or "%s" container parameter',
                    SymfonyParameters::NAMESPACES_PARAMETER_NAME,
                    SymfonyParameters::NAMESPACES_EXTENDED_PARAMETER_NAME
                )
            );
        }

        return $annotationDriver
            ->addAnnotationClass(CatalogGroup::class)
            ->addAnnotationClass(D7Item::class)
            ->addAnnotationClass(HlbItem::class)
            ->addAnnotationClass(HlbReferenceItem::class)
            ->addAnnotationClass(IblockElement::class)
            ->addAnnotationClass(IblockSection::class)
            ->addAnnotationClass(Cache::class)
            ->addAnnotationClass(Hydrator::class)
            ->addDirectoryName('Model');
    }

    /**
     * @param ContainerBuilder $container
     *
     * @return AnnotationProcessorRegistry
     */
    protected function getAnnotationProcessorRegistry(ContainerBuilder $container): AnnotationProcessorRegistry
    {
        return (new AnnotationProcessorRegistry())->set(new IblockAnnotationProcessor($container))
            ->set(new D7OrmAnnotationProcessor($container))
            ->set(new HydratorAnnotationProcessor($container))
            ->set(new CacheAnnotationProcessor($container))
            ->set(new CatalogGroupAnnotationProcessor($container));
    }

    /**
     * @param ContainerBuilder $container
     *
     * @return string
     */
    protected function getSourcesDir(ContainerBuilder $container): string
    {
        return (string)$container->getParameter('kernel.project_dir');
    }

    /**
     * @param ContainerBuilder $container   Контейнер.
     * @param ArrayCollection  $definitions Definitions.
     * @param string           $modelClass  Класс модели.
     *
     * @return void
     */
    protected function defineOrmServices(ContainerBuilder $container, ArrayCollection $definitions, string $modelClass): void
    {
        $repositoryRegistry = $container->getDefinition(RepositoryRegistryInterface::class);

        $this->defineDataManager(
            $container,
            $definitions
        );
        $this->defineFactory(
            $container,
            $definitions
        );

        $repositoryServiceId = $this->defineRepository(
            $container,
            $definitions
        );
        $bitrixCacheServiceId = $this->defineBitrixCache($container, $definitions, $repositoryServiceId, $modelClass);

        $cacheServiceId = $this->defineCache(
            $container,
            $definitions,
            $bitrixCacheServiceId ?? $repositoryServiceId,
            $modelClass,
            $repositoryServiceId
        );

        $hydratorServiceId = $this->defineHydrator(
            $container,
            $definitions,
            $cacheServiceId ?? $repositoryServiceId,
            $modelClass
        );

        $definitionId = $repositoryServiceId;
        if ($hydratorServiceId) {
            $definitionId = $hydratorServiceId;
        } elseif ($cacheServiceId) {
            $definitionId = $cacheServiceId;
        }

        $repositoryRegistry->addMethodCall(
            'set',
            [
                $modelClass,
                $definitionId,
            ]
        );
    }

    /**
     * @param ContainerBuilder $container
     * @param ArrayCollection $definitions
     */
    protected function defineDataManager(ContainerBuilder $container, ArrayCollection $definitions): void
    {
        foreach ($definitions as $definition) {
            if ($definition instanceof DataManagerDefinition) {
                $symfonyDefinition = $definition->getDefinition();
                $container->setDefinition(
                    $definition->getId(),
                    $symfonyDefinition
                );
            }
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param ArrayCollection $definitions
     *
     * @return string
     */
    protected function defineFactory(ContainerBuilder $container, ArrayCollection $definitions): string
    {
        foreach ($definitions as $definition) {
            if ($definition instanceof FactoryDefinition) {
                $symfonyDefinition = $definition->getDefinition();
                $container->setDefinition(
                    $definition->getId(),
                    $symfonyDefinition
                );

                return $definition->getId();
            }
        }

        throw new FactoryDefinitionException('Factory definition not found');
    }

    /**
     * @param ContainerBuilder $container
     * @param ArrayCollection $definitions
     *
     * @return string
     */
    protected function defineRepository(ContainerBuilder $container, ArrayCollection $definitions): string
    {
        foreach ($definitions as $definition) {
            if ($definition instanceof RepositoryDefinition) {
                $symfonyDefinition = $definition->getDefinition();
                $container->setDefinition(
                    $definition->getId(),
                    $symfonyDefinition
                );

                return $definition->getId();
            }
        }

        throw new RepositoryDefinitionException('Repository definition not found');
    }

    /**
     * @param ContainerBuilder $container
     * @param ArrayCollection $definitions
     * @param string          $parentServiceId
     * @param string          $modelClass
     *
     * @return string|null
     */
    protected function defineBitrixCache(ContainerBuilder $container, ArrayCollection $definitions, string $parentServiceId, string $modelClass): ?string
    {
        $proxyDefinitionId = null;
        foreach ($definitions as $definition) {
            if ($definition instanceof BitrixCacheDefinition) {
                $cacheDefinitionId = $definition->getId();
                $symfonyDefinition = $definition->getDefinition();
                $container->setDefinition(
                    $cacheDefinitionId,
                    $symfonyDefinition
                );

                $proxyDefinition = new Definition();
                $proxyDefinition
                    ->setClass(CacheProxy::class)
                    ->setArgument('$excludedMethods', $definition->getExcludedMethods())
                    ->addMethodCall('setSubject', [new Reference($parentServiceId)])
                    ->addMethodCall('setRepository', [new Reference($parentServiceId)])
                    ->addMethodCall('setCache', [new Reference($cacheDefinitionId)])
                    ->setPublic(true);

                $proxyDefinitionId = ServiceIdHelper::getBitrixCacheProxyServiceId($modelClass);

                $container->setDefinition(
                    $proxyDefinitionId,
                    $proxyDefinition
                );
                break;
            }
        }

        return $proxyDefinitionId;
    }

    /**
     * @param ContainerBuilder $container
     * @param ArrayCollection $definitions
     * @param string $parentServiceId
     * @param string $modelClass
     * @param string $repositoryServiceId
     *
     * @return string|null
     */
    protected function defineCache(
        ContainerBuilder $container,
        ArrayCollection $definitions,
        string $parentServiceId,
        string $modelClass,
        string $repositoryServiceId
    ): ?string
    {
        $proxyDefinitionId = null;
        foreach ($definitions as $definition) {
            if ($definition instanceof ArrayCacheDefinition) {
                $cacheDefinitionId = $definition->getId();
                $symfonyDefinition = $definition->getDefinition();
                $container->setDefinition(
                    $cacheDefinitionId,
                    $symfonyDefinition
                );

                $proxyDefinition = new Definition();
                $proxyDefinition
                    ->setClass(CacheProxy::class)
                    ->setArgument('$excludedMethods', $definition->getExcludedMethods())
                    ->addMethodCall('setSubject', [new Reference($parentServiceId)])
                    ->addMethodCall('setRepository', [new Reference($repositoryServiceId)])
                    ->addMethodCall('setCache', [new Reference($cacheDefinitionId)])
                    ->setPublic(true);
                $proxyDefinitionId = ServiceIdHelper::getCacheProxyServiceId($modelClass);

                $container->setDefinition(
                    $proxyDefinitionId,
                    $proxyDefinition
                );

                break;
            }
        }

        return $proxyDefinitionId;
    }

    /**
     * @param ContainerBuilder $container
     * @param ArrayCollection $definitions
     * @param string $parentServiceId
     * @param string $modelClass
     *
     * @return string|null
     */
    protected function defineHydrator(ContainerBuilder $container, ArrayCollection $definitions, string $parentServiceId, string $modelClass): ?string
    {
        $proxyDefinitionId = null;
        foreach ($definitions as $definition) {
            if ($definition instanceof HydratorDefinition) {
                $symfonyDefinition = $definition->getDefinition();
                $container->setDefinition(
                    $definition->getId(),
                    $symfonyDefinition
                );

                $proxyDefinition = new Definition();
                $proxyDefinition->setClass(HydratorProxy::class)
                    ->addMethodCall('setHydrator', [new Reference($definition->getId())])
                    ->addMethodCall('setRepository', [new Reference($parentServiceId)])
                    ->setPublic(true);
                $proxyDefinitionId = ServiceIdHelper::getHydratorProxyServiceId($modelClass);
                $container->setDefinition(
                    $proxyDefinitionId,
                    $proxyDefinition
                );
                break;
            }
        }

        return $proxyDefinitionId;
    }
}
