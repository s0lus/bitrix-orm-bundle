<?php

namespace Prokl\BitrixOrmBundle\Registry;

use Prokl\BitrixOrmBundle\Base\Repository\CdbResultRepository;
use Prokl\BitrixOrmBundle\Base\Repository\D7Repository;
use Prokl\BitrixOrmBundle\Exception\Registry\RepositoryNotRegisteredException;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;

/**
 * Class RepositoryRegistry
 * @package Prokl\BitrixOrmBundle\Registry
 *
 * @psalm-suppress PossiblyNullArgument
 */
class RepositoryRegistry implements RepositoryRegistryInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var ArrayCollection
     */
    protected $repositories;

    /**
     * RepositoryRegistry constructor.
     */
    public function __construct()
    {
        $this->repositories = new ArrayCollection();
    }

    /**
     * @param string $class
     *
     * @return D7Repository|CdbResultRepository
     * @throws RepositoryNotRegisteredException
     * @throws ServiceCircularReferenceException
     */
    public function get(string $class)
    {
        if (!$this->repositories->containsKey($class)) {
            throw new RepositoryNotRegisteredException(\sprintf('Repository for "%s" not registered', $class));
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->container->get($this->repositories->get($class));
    }

    /**
     * @param string $class
     * @param string $serviceId
     */
    public function set(string $class, string $serviceId): void
    {
        $this->repositories->set($class, $serviceId);
    }
}
