<?php

namespace Prokl\BitrixOrmBundle\Driver;

use Prokl\BitrixOrmBundle\Exception\Parser\ClassNameNotFoundException;

/**
 * Class FileNameParser
 * @package Prokl\BitrixOrmBundle\Driver
 */
class FileNameParser implements FileParserInterface
{
    /**
     * @var string
     */
    protected $fileName;

    /**
     * @var string
     */
    protected $namespacePrefix;

    /**
     * @var string
     */
    protected $namespaceRoot;

    /**
     * FakeFileParser constructor.
     *
     * @param string $fileName
     * @param string $namespacePrefix
     * @param string $namespaceRoot
     */
    public function __construct(string $fileName, string $namespacePrefix, string $namespaceRoot)
    {
        $this->fileName        = $fileName;
        $this->namespacePrefix = $namespacePrefix;
        $this->namespaceRoot   = realpath($namespaceRoot);
    }

    /**
     * @return string
     * @throws ClassNameNotFoundException
     */
    public function getFqcn(): string
    {
        $realPath = realpath($this->namespaceRoot);

        $relativePath = str_ireplace($realPath, '', $this->fileName);
        $pathInfo     = pathinfo($relativePath);

        $className = \implode(
            DIRECTORY_SEPARATOR,
            [
                \trim($this->namespacePrefix, '/\\'),
                \trim($pathInfo['dirname'], DIRECTORY_SEPARATOR),
                $pathInfo['filename'],
            ]
        );

        $className = str_replace(DIRECTORY_SEPARATOR, '\\', $className);

        try {
            new \ReflectionClass($className);
        } catch (\ReflectionException $e) {
            throw new ClassNameNotFoundException($e->getMessage());
        }

        return $className;
    }
}
