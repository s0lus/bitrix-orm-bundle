<?php

namespace Prokl\BitrixOrmBundle\Driver;

use Prokl\BitrixOrmBundle\Dto\AnnotatedFile;
use Prokl\BitrixOrmBundle\Dto\AnnotationDiscoveryResult;
use Prokl\BitrixOrmBundle\Dto\Directory;
use Prokl\BitrixOrmBundle\Dto\NamespacePrefix;
use Prokl\BitrixOrmBundle\Exception\AnnotationDriver\AnnotationsNotDefinedException;
use Prokl\BitrixOrmBundle\Exception\AnnotationDriver\ClassNotFoundException;
use Prokl\BitrixOrmBundle\Exception\AnnotationDriver\DirectoriesNotFoundException;
use Prokl\BitrixOrmBundle\Exception\AnnotationDriver\FilesNotFoundException;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class AnnotationDriver
 * @package Prokl\BitrixOrmBundle\Driver
 */
class AnnotationDriver implements AnnotationDriverInterface
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var string[]
     */
    private $directoryNames = [];

    /**
     * @var string[] | null
     */
    private $annotationClasses;

    /**
     * @var array | null
     */
    private $namespaces = [];

    /**
     * EntityAnnotationDriver constructor.
     *
     * @param Reader $reader
     * @param string $rootDir
     */
    public function __construct(Reader $reader, string $rootDir)
    {
        $this->reader  = $reader;
        $this->rootDir = $rootDir;
        AnnotationRegistry::registerLoader('class_exists');
    }

    /**
     * @param NamespacePrefix $namespacePrefix
     *
     * @return AnnotationDriverInterface
     */
    public function addNamespace(NamespacePrefix $namespacePrefix): AnnotationDriverInterface
    {
        $this->namespaces[] = $namespacePrefix;

        return $this;
    }

    /**
     * @return array
     */
    public function getNamespaces(): array
    {
        return (array)$this->namespaces;
    }

    /**
     * @param string $className
     *
     * @return AnnotationDriverInterface
     * @throws ClassNotFoundException
     */
    public function addAnnotationClass(string $className): AnnotationDriverInterface
    {
        if (false === $this->registerAnnotation($className)) {
            throw new ClassNotFoundException(\sprintf('Class %s autoload failed', $className));
        }
        $this->annotationClasses[$className] = $className;

        return $this;
    }

    /**
     * @param string $directoryName
     *
     * @return AnnotationDriverInterface
     */
    public function addDirectoryName(string $directoryName): AnnotationDriverInterface
    {
        $this->directoryNames[] = $directoryName;

        return $this;
    }

    /**
     * @return ArrayCollection
     * @throws AnnotationsNotDefinedException
     */
    public function discover(): ArrayCollection
    {
        if (empty($this->annotationClasses)) {
            throw new AnnotationsNotDefinedException('Annotation classes not defined');
        }

        $result = new ArrayCollection();

        /**
         * @var string $namespacePrefix
         * @var string $directoryToScan
         */
        foreach ($this->getDirectoriesToScan() as $directoryToScan => $namespacePrefix) {
            try {
                $directories = $this->findDirectories($directoryToScan);
                $files       = $this->findFiles($directories, $directoryToScan, $namespacePrefix);
                /** @var AnnotatedFile $fileInfo */
                foreach ($files as $fileInfo) {
                    $discoveryResult = (new AnnotationDiscoveryResult())->setClass($fileInfo->getClassName());

                    $annotations = new ArrayCollection();
                    foreach ($fileInfo->getAnnotations() as $annotation) {
                        $annotations->add($annotation);
                    }

                    $discoveryResult->setClassAnnotations($annotations);
                    $result->set($discoveryResult->getClass(), $discoveryResult);
                }
            } catch (FilesNotFoundException|DirectoriesNotFoundException $e) {
            }
        }

        return $result;
    }

    /**
     * @param string $directoryToScan
     *
     * @return ArrayCollection
     * @throws DirectoriesNotFoundException
     */
    protected function findDirectories(string $directoryToScan): ArrayCollection
    {
        $finder = $this->getDirectoryFinder($directoryToScan);

        foreach ($this->directoryNames as $name) {
            $finder->addName($name);
        }

        $result = $finder->find();
        if ($result->isEmpty()) {
            throw new DirectoriesNotFoundException(
                \sprintf(
                    'Directories with names %s not found',
                    \implode(',', $this->directoryNames)
                )
            );
        }

        return $result;
    }

    /**
     * @param ArrayCollection $directories
     * @param string          $namespaceRoot
     * @param string|null     $namespacePrefix
     *
     * @return ArrayCollection
     * @throws FilesNotFoundException
     */
    protected function findFiles(
        ArrayCollection $directories,
        string $namespaceRoot,
        string $namespacePrefix = null
    ): ArrayCollection
    {
        $scanner = $this->getScanner();
        $scanner->scan($this->annotationClasses);

        $scanner->setNamespaceRoot($namespaceRoot);
        if (null !== $namespacePrefix) {
            $scanner->setNamespacePrefix($namespacePrefix);
        }

        $directoryPaths = [];
        /** @var Directory $directory */
        foreach ($directories as $directory) {
            $directoryPaths[] = $directory->getPath();
            $scanner->in($directory->getPath());
        }

        $result = $scanner->run();
        if ($result->isEmpty()) {
            throw new FilesNotFoundException(
                \sprintf(
                    'No files found with required annotations in %s',
                    \implode(', ', $directoryPaths)
                )
            );
        }

        return $result;
    }

    /**
     * @return Scanner
     */
    protected function getScanner(): ScannerInterface
    {
        return new Scanner($this->reader);
    }

    /**
     * @param string $rootDir
     *
     * @return DirectoryFinderInterface
     */
    protected function getDirectoryFinder(string $rootDir): DirectoryFinderInterface
    {
        return new DirectoryFinder($rootDir);
    }

    /**
     * @param string $className
     *
     * @return boolean
     */
    protected function registerAnnotation(string $className): bool
    {
        return AnnotationRegistry::loadAnnotationClass($className);
    }

    /**
     * @return array
     */
    protected function getDirectoriesToScan(): array
    {
        $result = [];
        if (!empty($this->namespaces)) {
            /** @var NamespacePrefix $namespacePrefix */
            foreach ($this->namespaces as $namespacePrefix) {
                /**
                 * удаляем путь до папки с проектом, если он есть
                 */
                $dir = str_replace($this->rootDir . '/', '', $namespacePrefix->getDir());

                /**
                 * если впереди остался '/', то директория лежит не внутри проекта,
                 * и к ней не нужно добавлять путь до проекта
                 */
                if (0 !== mb_strpos($dir, '/')) {
                    $dir = \implode(
                        DIRECTORY_SEPARATOR,
                        [
                            \rtrim($this->rootDir, DIRECTORY_SEPARATOR),
                            trim($dir, DIRECTORY_SEPARATOR),
                        ]
                    );
                }

                $result[$dir] = $namespacePrefix->getPrefix();
            }
        } else {
            $result[$this->rootDir] = null;
        }

        return $result;
    }
}
