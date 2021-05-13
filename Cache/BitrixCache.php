<?php

namespace Prokl\BitrixOrmBundle\Cache;

use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\FileRepositoryAwareInterface;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\HasCodeInterface;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\HasIdInterface;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\HasXmlIdInterface;
use Prokl\BitrixOrmBundle\Base\Repository\FileRepository;
use WebArch\BitrixCache\BitrixCache as Cache;

/**
 * Class BitrixCacheStrategy
 * @package Prokl\BitrixOrmBundle\Cache
 *
 * @todo    один и тот же элемент кеша при получении по ID, CODE, XML_ID
 */
class BitrixCache implements BitrixCacheInterface
{
    /**
     * @var string
     */
    protected $modelClass;

    /**
     * @var FileRepository
     */
    protected $fileRepository;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var integer
     */
    protected $cacheTime;

    /**
     * @var string
     */
    protected $tag;

    /**
     * @var string|null
     */
    protected $collectionTag;

     /**
     * @var boolean
     */
    protected $injectFileRepository;

    /**
     * BitrixCacheStrategy constructor.
     *
     * @param string      $modelClass
     * @param integer     $cacheTime
     * @param string      $tag
     * @param string|null $collectionTag
     */
    public function __construct(
        string $modelClass,
        int $cacheTime,
        string $tag = null,
        string $collectionTag = null
    )
    {
        $this->modelClass     = $modelClass;
        $this->key            = \str_replace('\\', '_', $modelClass);
        $this->cacheTime      = $cacheTime;
        $this->tag            = $tag;
        $this->collectionTag  = $collectionTag;
    }

    /**
     * @param FileRepository $fileRepository
     */
    public function setFileRepository(FileRepository $fileRepository): void
    {
        $this->fileRepository = $fileRepository;
    }

    /**
     * @param string   $method
     * @param array    $parameters
     * @param callable $callback
     *
     * @return mixed
     * @throws Exception
     */
    public function get(string $method, array $parameters, callable $callback)
    {
        $cache = $this->getBitrixCache()
                      ->withId($this->getSerializedKey($method, $parameters))
                      ->withTime($this->cacheTime);

        if ($this->tag) {
            $cache->withTag($this->tag);
        }

        return $this->wakeUpObjects(
            $cache->resultOf($callback)['result']
        );
    }

    /**
     * @param integer      $id
     * @param callable $callback
     *
     * @return HasIdInterface
     * @throws Exception
     */
    public function getById(int $id, callable $callback): HasIdInterface
    {
        $cache = $this->getBitrixCache()
                      ->withId($this->getIdKey($id))
                      ->withTime($this->cacheTime);

        if ($this->tag) {
            $cache->withTag($this->tag)
                  ->withTag($this->getIdTag($id));
        }

        return $this->wakeUpObjects(
            $cache->resultOf($callback)['result']
        );
    }

    /**
     * @param string   $code
     * @param callable $callback
     *
     * @return HasCodeInterface
     * @throws Exception
     */
    public function getByCode(string $code, callable $callback): HasCodeInterface
    {
        $cache = $this->getBitrixCache()
                      ->withId($this->getCodeKey($code))
                      ->withTime($this->cacheTime);

        if ($this->tag) {
            $cache->withTag($this->tag)
                  ->withTag($this->getCodeTag($code));
        }

        return $this->wakeUpObjects(
            $cache->resultOf($callback)['result']
        );
    }

    /**
     * @param string   $xmlId
     * @param callable $callback
     *
     * @return HasXmlIdInterface
     * @throws Exception
     */
    public function getByXmlId(string $xmlId, callable $callback): HasXmlIdInterface
    {
        $cache = $this->getBitrixCache()
                      ->withId($this->getXmlIdKey($xmlId))
                      ->withTime($this->cacheTime);

        if ($this->tag) {
            $cache->withTag($this->tag)
                  ->withTag($this->getXmlIdTag($xmlId));
        }

        return $this->wakeUpObjects(
            $cache->resultOf($callback)['result']
        );
    }

    /**
     * @param HasIdInterface $object
     * @param callable       $callback
     *
     * @return ArrayCollection
     * @throws Exception
     */
    public function getByObject(HasIdInterface $object, callable $callback): ArrayCollection
    {
        $cache = $this->getBitrixCache()
                      ->withId($this->getObjectKey($object))
                      ->withTime($this->cacheTime);

        if ($this->tag) {
            $cache->withTag($this->tag);
        }

        if ($objectTags = $this->getObjectTags($object)) {
            foreach ($objectTags as $objectTag) {
                $cache->withTag($objectTag);
            }
        }

        return $this->wakeUpObjects(
            $cache->resultOf($callback)['result']
        );
    }

    /**
     * @param HasIdInterface $item
     *
     * @return boolean
     */
    public function set(HasIdInterface $item): bool
    {
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
        return true;
    }

    /**
     * @param integer $id
     *
     * @return boolean
     */
    public function clear(int $id): bool
    {
        return true;
    }

    /**
     * @return string
     */
    public function getTag(): string
    {
        return $this->tag;
    }

    /**
     * @param integer     $id
     * @param string|null $tag
     *
     * @return string
     */
    public function getIdTag(int $id, string $tag = null): string
    {
        return ($tag ?? $this->tag) . ':' . $id;
    }

    /**
     * @param string      $code
     * @param string|null $tag
     *
     * @return string
     */
    public function getCodeTag(string $code, string $tag = null): string
    {
        return ($tag ?? $this->tag) . ':code:' . $code;
    }

    /**
     * @param string      $xmlId
     * @param string|null $tag
     *
     * @return string
     */
    public function getXmlIdTag(string $xmlId, string $tag = null): string
    {
        return ($tag ?? $this->tag) . ':xmlid:' . $xmlId;
    }

    /**
     * @param HasIdInterface $item
     *
     * @return array
     */
    public function getObjectTags(HasIdInterface $item): array
    {
        $result = [];
        if ($this->collectionTag) {
            $result[] = $this->getCollectionIdTag($item->getId());
        }

        return $result;
    }

    /**
     * @param integer $id
     *
     * @return string
     */
    public function getCollectionIdTag(int $id): string
    {
        return $this->getIdTag($id, $this->collectionTag);
    }

    /**
     * @param HasIdInterface $object
     *
     * @return boolean
     */
    public function clearByObject(HasIdInterface $object): bool
    {
        return true;
    }

    /**
     * @return Cache
     */
    protected function getBitrixCache(): Cache
    {
        return new Cache();
    }

    /**
     * @param string $method
     * @param array  $parameters
     *
     * @return string
     */
    protected function getSerializedKey(string $method, array $parameters): string
    {
        return $this->key . \json_encode(\compact('method', 'parameters'));
    }

    /**
     * @param integer $id
     *
     * @return string
     */
    protected function getIdKey(int $id): string
    {
        return $this->key . $id;
    }

    /**
     * @param string $code
     *
     * @return string
     */
    protected function getCodeKey(string $code): string
    {
        return $this->key . '_code_' . $code;

    }

    /**
     * @param string $xmlId
     *
     * @return string
     */
    protected function getXmlIdKey(string $xmlId): string
    {
        return $this->key . '_xmlid_' . $xmlId;
    }

    /**
     * @param HasIdInterface $object
     *
     * @return string
     */
    protected function getObjectKey(HasIdInterface $object): string
    {
        return $this->key . \str_replace('\\', '_', \get_class($object)) . '_' . $object->getId();
    }

    /**
     * @param $items
     *
     * @return mixed
     * @throws \ReflectionException
     */
    protected function wakeUpObjects($items)
    {
        $objects = is_iterable($items) ? $items : [$items];

        foreach ($objects as $object) {
            if ($object instanceof $this->modelClass && $this->needInjectFileRepository()) {
                /** @var FileRepositoryAwareInterface $item */
                $object::setFileRepository($this->fileRepository);
            }
        }

        return $items;
    }

    /**
     * @return bool
     * @throws \ReflectionException
     */
    protected function needInjectFileRepository(): bool
    {
        if (null === $this->injectFileRepository) {
            $reflection                 = new \ReflectionClass($this->modelClass);
            $this->injectFileRepository = $reflection->implementsInterface(FileRepositoryAwareInterface::class);
        }

        return $this->injectFileRepository;
    }
}
