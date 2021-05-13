<?php

namespace Prokl\BitrixOrmBundle\Cache;

use Doctrine\Common\Collections\ArrayCollection;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\HasCodeInterface;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\HasIdInterface;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\HasXmlIdInterface;

/**
 * Interface CacheInterface
 * @package Prokl\BitrixOrmBundle\Cache
 */
interface CacheInterface
{
    /**
     * @param string   $method
     * @param array    $parameters
     * @param callable $callback
     *
     * @return mixed
     */
    public function get(string $method, array $parameters, callable $callback);

    /**
     * @param integer      $id
     * @param callable $callback
     *
     * @return HasIdInterface
     */
    public function getById(int $id, callable $callback): HasIdInterface;

    /**
     * @param string   $code
     * @param callable $callback
     *
     * @return HasCodeInterface
     */
    public function getByCode(string $code, callable $callback): HasCodeInterface;

    /**
     * @param string   $xmlId
     * @param callable $callback
     *
     * @return HasXmlIdInterface
     */
    public function getByXmlId(string $xmlId, callable $callback): HasXmlIdInterface;

    /**
     * @param HasIdInterface $object
     * @param callable       $callback
     *
     * @return ArrayCollection
     */
    public function getByObject(HasIdInterface $object, callable $callback): ArrayCollection;

    /**
     * @param HasIdInterface $item
     *
     * @return boolean
     */
    public function set(HasIdInterface $item): bool;

    /**
     * @param HasIdInterface  $object
     * @param ArrayCollection $data
     *
     * @return boolean
     */
    public function setByObject(HasIdInterface $object, ArrayCollection $data): bool;

    /**
     * @param integer $id
     *
     * @return boolean
     */
    public function clear(int $id): bool;

    /**
     * @param HasIdInterface $object
     *
     * @return boolean
     */
    public function clearByObject(HasIdInterface $object): bool;
}
