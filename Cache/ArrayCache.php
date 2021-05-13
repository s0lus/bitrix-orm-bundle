<?php

namespace Prokl\BitrixOrmBundle\Cache;

use Doctrine\Common\Collections\ArrayCollection;
use Prokl\BitrixOrmBundle\Base\Model\BitrixArrayItemBase;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\HasCodeInterface;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\HasIdInterface;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\HasXmlIdInterface;

/**
 * Class ArrayCacheStrategy
 * @package Prokl\BitrixOrmBundle\Cache
 */
class ArrayCache implements CacheInterface
{
    /**
     * @var ArrayCollection
     */
    protected $storage;

    /**
     * @var ArrayCollection
     */
    protected $idStorage;

    /**
     * @var ArrayCollection
     */
    protected $codeStorage;

    /**
     * @var ArrayCollection
     */
    protected $xmlIdStorage;

    /**
     * @var \SplObjectStorage
     */
    protected $collectionStorage;

    /**
     * ArrayCachingStrategy constructor.
     */
    public function __construct()
    {
        $this->storage           = new ArrayCollection();
        $this->idStorage         = new ArrayCollection();
        $this->codeStorage       = new ArrayCollection();
        $this->xmlIdStorage      = new ArrayCollection();
        $this->collectionStorage = new \SplObjectStorage();
    }

    /**
     * @param string   $method
     * @param array    $parameters
     * @param callable $callback
     *
     * @return mixed
     */
    public function get(string $method, array $parameters, callable $callback)
    {
        $key = \json_encode(\compact('method', 'parameters'));

        if (!$this->contains($key)) {
            $result = $callback()['result'];
            if ($result instanceof ArrayCollection) {
                foreach ($result as $item) {
                    $this->set($item);
                }
            } elseif ($result instanceof BitrixArrayItemBase) {
                $this->set($result);
            }

            $this->storage->offsetSet($key, $result);
        }

        return $this->storage->offsetGet($key);
    }

    /**
     * @param integer      $id
     * @param callable $callback
     *
     * @return HasIdInterface
     */
    public function getById(int $id, callable $callback): HasIdInterface
    {
        if (!$this->containsId($id)) {
            $result = $callback()['result'];
            $this->set($result);
        } else {
            $result = $this->idStorage->offsetGet($id);
        }

        return $result;
    }

    /**
     * @param string   $code
     * @param callable $callback
     *
     * @return HasCodeInterface
     */
    public function getByCode(string $code, callable $callback): HasCodeInterface
    {
        if (!$this->containsCode($code)) {
            $result = $callback()['result'];
            $this->set($result);
        } else {
            $result = $this->codeStorage->offsetGet($code);
        }

        return $result;
    }

    /**
     * @param string   $xmlId
     * @param callable $callback
     *
     * @return HasXmlIdInterface
     */
    public function getByXmlId(string $xmlId, callable $callback): HasXmlIdInterface
    {
        if (!$this->containsXmlId($xmlId)) {
            $result = $callback()['result'];
            $this->set($result);
        } else {
            $result = $this->xmlIdStorage->offsetGet($xmlId);
        }

        return $result;
    }

    /**
     * @param HasIdInterface $object
     * @param callable       $callback
     *
     * @return ArrayCollection
     */
    public function getByObject(HasIdInterface $object, callable $callback): ArrayCollection
    {
        if (!$this->containsObject($object)) {
            $result = $callback()['result'];
            $this->setByObject($object, $result);
        } else {
            $result = $this->collectionStorage->offsetGet($object);
        }

        return $result;
    }

    /**
     * @param integer $id
     *
     * @return boolean
     */
    public function clear(int $id): bool
    {
        if ($this->containsId($id)) {
            $item = $this->getById(
                $id,
                static function () {
                }
            );
            $this->idStorage->offsetUnset($item->getId());

            if ($item instanceof HasCodeInterface && $item->getCode()) {
                $this->codeStorage->offsetUnset($item->getCode());
            }

            if ($item instanceof HasXmlIdInterface && $item->getXmlId()) {
                $this->xmlIdStorage->offsetUnset($item->getXmlId());
            }
        }

        return true;
    }

    /**
     * @param HasIdInterface $object
     *
     * @return boolean
     */
    public function clearByObject(HasIdInterface $object): bool
    {
        if ($this->containsObject($object)) {
            $items = $this->getByObject(
                $object,
                function () {
                }
            );

            /** @var HasIdInterface $item */
            foreach ($items as $item) {
                $this->clear($item->getId());
            }

            $this->collectionStorage->offsetUnset($object);
        }

        return true;
    }

    /**
     * @param string $key
     *
     * @return boolean
     */
    protected function contains(string $key): bool
    {
        return $this->storage->offsetExists($key);
    }

    /**
     * @param integer $id
     *
     * @return boolean
     */
    protected function containsId(int $id): bool
    {
        return $this->idStorage->offsetExists($id);
    }

    /**
     * @param string $code
     *
     * @return boolean
     */
    protected function containsCode(string $code): bool
    {
        return $this->codeStorage->offsetExists($code);
    }

    /**
     * @param string $xmlId
     *
     * @return boolean
     */
    protected function containsXmlId(string $xmlId): bool
    {
        return $this->xmlIdStorage->offsetExists($xmlId);
    }

    /**
     * @param object $object
     *
     * @return boolean
     */
    protected function containsObject($object): bool
    {
        return $this->collectionStorage->contains($object);
    }

    /**
     * @param HasIdInterface $item
     *
     * @return mixed
     */
    public function set(HasIdInterface $item): bool
    {
        $this->idStorage->offsetSet($item->getId(), $item);

        if ($item instanceof HasCodeInterface && $item->getCode()) {
            $this->codeStorage->offsetSet($item->getCode(), $item);
        }

        if ($item instanceof HasXmlIdInterface && $item->getXmlId()) {
            $this->xmlIdStorage->offsetSet($item->getXmlId(), $item);
        }

        return true;
    }

    /**
     * @param HasIdInterface  $object
     * @param ArrayCollection $data
     *
     * @return boolean
     */
    public function setByObject(HasIdInterface $object, ArrayCollection $data): bool
    {
        $this->collectionStorage->offsetSet($object, $data);

        /** @var HasIdInterface $item */
        foreach ($data as $item) {
            $this->set($item);
        }

        return true;
    }
}
