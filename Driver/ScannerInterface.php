<?php

namespace Prokl\BitrixOrmBundle\Driver;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Interface ScannerInterface
 * @package Prokl\BitrixOrmBundle\Driver
 */
interface ScannerInterface
{
    /**
     * @param string $path
     *
     * @return ScannerInterface
     */
    public function in(string $path): ScannerInterface;

    /**
     * @param string $prefix
     *
     * @return ScannerInterface
     */
    public function setNamespacePrefix(string $prefix): ScannerInterface;

    /**
     * @param string $root
     *
     * @return ScannerInterface
     */
    public function setNamespaceRoot(string $root): ScannerInterface;

    /**
     * @param string[] $classNames
     *
     * @return ScannerInterface
     */
    public function scan(array $classNames): ScannerInterface;

    /**
     * @return ArrayCollection
     */
    public function run(): ArrayCollection;
}
