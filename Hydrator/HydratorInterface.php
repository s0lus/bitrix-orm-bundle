<?php

namespace Prokl\BitrixOrmBundle\Hydrator;

/**
 * Interface HydratorInterface
 * @package Prokl\BitrixOrmBundle\Hydrator
 */
interface HydratorInterface
{
    /**
     * @param object $object
     *
     * @return object
     */
    public function fill($object);
}
