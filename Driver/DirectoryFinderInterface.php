<?php

namespace Prokl\BitrixOrmBundle\Driver;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Interface DirectoryFinderInterface
 * @package Prokl\BitrixOrmBundle\Driver
 */
interface DirectoryFinderInterface
{
    /**
     * @param string $name
     *
     * @return DirectoryFinderInterface
     */
    public function addName(string $name): DirectoryFinderInterface;

    /**
     * @return ArrayCollection
     */
    public function find(): ArrayCollection;
}
