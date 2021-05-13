<?php

namespace Prokl\BitrixOrmBundle\Registry;

use Prokl\BitrixOrmBundle\Base\Repository\CdbResultRepository;
use Prokl\BitrixOrmBundle\Base\Repository\D7Repository;
use Prokl\BitrixOrmBundle\Exception\Registry\RepositoryNotRegisteredException;
use Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;

/**
 * Interface RepositoryRegistryInterface
 * @package Prokl\BitrixOrmBundle\Registry
 */
interface RepositoryRegistryInterface
{
    /**
     * @param string $class
     *
     * @return D7Repository|CdbResultRepository
     * @throws RepositoryNotRegisteredException
     * @throws ServiceCircularReferenceException
     */
    public function get(string $class);

    /**
     * @param string $class
     * @param string $serviceId
     */
    public function set(string $class, string $serviceId): void;
}
