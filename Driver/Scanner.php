<?php

namespace Prokl\BitrixOrmBundle\Driver;

use Prokl\BitrixOrmBundle\Dto\AnnotatedFile;
use Prokl\BitrixOrmBundle\Exception\Parser\ParserExceptionInterface;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class Scanner
 * @package Prokl\BitrixOrmBundle\Driver
 */
class Scanner implements ScannerInterface
{
    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var string
     */
    protected $namespacePrefix;

    /**
     * @var string
     */
    protected $namespaceRoot;

    /**
     * @var string[]
     */
    protected $paths = [];

    /**
     * @var string[]
     */
    protected $classNames = [];

    /**
     * Scanner constructor.
     *
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param string $path
     *
     * @return ScannerInterface
     */
    public function in(string $path): ScannerInterface
    {
        $this->paths[] = $path;

        return $this;
    }

    /**
     * @param string $prefix
     *
     * @return ScannerInterface
     */
    public function setNamespacePrefix(string $prefix): ScannerInterface
    {
        $this->namespacePrefix = $prefix;

        return $this;
    }

    /**
     * @param string $root
     *
     * @return ScannerInterface
     */
    public function setNamespaceRoot(string $root): ScannerInterface
    {
        $this->namespaceRoot = $root;

        return $this;
    }

    /**
     * @param string[] $classNames
     *
     * @return ScannerInterface
     */
    public function scan(array $classNames): ScannerInterface
    {
        $this->classNames = $classNames;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function run(): ArrayCollection
    {
        $result = new ArrayCollection();

        $finder = new Finder();

        $files = $finder->in($this->paths)
                        ->files()
                        ->name('*.php')
                        ->getIterator();

        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            try {
                $className = $this->getFileParser($file->getRealPath())->getFqcn();

                $annotations = $this->reader->getClassAnnotations(new \ReflectionClass($className));
                foreach ($annotations as $annotation) {
                    if ($this->isValidAnnotation($annotation)) {
                        $result->add(
                            (new AnnotatedFile())->setAnnotations($annotations)
                                                 ->setClassName($className)
                                                 ->setPath($file->getRealPath())
                        );
                        break;
                    }
                }
            } catch (ParserExceptionInterface|\ReflectionException $e) {
                continue;
            }
        }

        return $result;
    }

    /**
     * @param $annotation
     *
     * @return boolean
     */
    protected function isValidAnnotation($annotation): bool
    {
        foreach ($this->classNames as $class) {
            if ($annotation instanceof $class) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $fileName
     *
     * @return FileParserInterface
     */
    protected function getFileParser(string $fileName): FileParserInterface
    {
        if (null === $this->namespacePrefix) {
            $parser = new FileParser($fileName);
        } else {
            $parser = new FileNameParser($fileName, $this->namespacePrefix, $this->namespaceRoot);
        }

        return $parser;
    }
}
