<?php

namespace Prokl\BitrixOrmBundle\Driver;

use Prokl\BitrixOrmBundle\Exception\Parser\ClassNameNotFoundException;
use Prokl\BitrixOrmBundle\Exception\Parser\IOException;

/**
 * Interface FileParserInterface
 * @package Prokl\BitrixOrmBundle\Driver
 */
interface FileParserInterface
{
    /**
     * @return string
     * @throws ClassNameNotFoundException
     * @throws IOException
     */
    public function getFqcn(): string;
}
