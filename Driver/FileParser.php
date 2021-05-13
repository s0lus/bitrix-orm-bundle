<?php

namespace Prokl\BitrixOrmBundle\Driver;

use Prokl\BitrixOrmBundle\Exception\Parser\ClassNameNotFoundException;
use Prokl\BitrixOrmBundle\Exception\Parser\IOException;

/**
 * Class FileParser
 * @package Prokl\BitrixOrmBundle\Driver
 */
class FileParser implements FileParserInterface
{
    /**
     * @var string
     */
    protected $fileName;

    /**
     * FileParser constructor.
     *
     * @param string $fileName
     */
    public function __construct(string $fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @return string
     * @throws ClassNameNotFoundException
     * @throws IOException
     */
    public function getFqcn(): string
    {
        $tokens = $this->getTokens($this->fileName);

        $namespace = $this->getNamespace($tokens);
        $class     = $this->getClassName($tokens);

        return \implode(
            '\\',
            \array_filter(
                [
                    $namespace,
                    $class,
                ]
            )
        );
    }

    /**
     * @param string $fileName
     *
     * @return array
     * @throws IOException
     */
    protected function getTokens(string $fileName): array
    {
        $contents = \file_get_contents($fileName);

        if (false === $contents) {
            throw new IOException(\sprintf('Cannot open file ' . $fileName));
        }

        return token_get_all($contents);
    }

    /**
     * @param array $tokens
     *
     * @return string
     */
    protected function getNamespace(array $tokens): string
    {
        $result = '';

        $count = \count($tokens);
        for ($i = 0; $i < $count; $i++) {
            $tokenId = $tokens[$i][0];
            if (T_NAMESPACE === $tokenId) {
                while (';' !== $tokens[$i][0]) {
                    $i++;
                    if (\in_array(
                        $tokens[$i][0],
                        [
                            T_STRING,
                            T_NS_SEPARATOR,
                        ],
                        true
                    )) {
                        $result .= $tokens[$i][1];
                    }
                }
            }

            if (T_CLASS === $tokenId) {
                break;
            }
        }

        return $result;
    }

    /**
     * @param array $tokens
     *
     * @return string
     * @throws ClassNameNotFoundException
     */
    protected function getClassName(array $tokens): string
    {
        $result = '';

        $count = \count($tokens);
        for ($i = 0; $i < $count; $i++) {
            if (T_CLASS !== $tokens[$i][0]) {
                continue;
            }

            while ('{' !== $tokens[$i]) {
                $i++;
                if (T_STRING === $tokens[$i][0]) {
                    $result = $tokens[$i][1];
                    break 2;
                }
            }
        }

        if ('' === $result) {
            throw new ClassNameNotFoundException(
                \sprintf('Class not found in %s', $this->fileName)
            );
        }

        return $result;
    }
}
