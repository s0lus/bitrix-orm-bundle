<?php

namespace Prokl\BitrixOrmBundle\Proxy;

use Prokl\BitrixOrmBundle\Base\Model\BitrixArrayItemBase;
use Prokl\BitrixOrmBundle\Base\Repository\CdbResultRepository;
use Prokl\BitrixOrmBundle\Base\Repository\D7Repository;
use Prokl\BitrixOrmBundle\Hydrator\HydratorInterface;
use Prokl\BitrixOrmBundle\Proxy\Traits\SourceRepoExtractorTrait;
use Doctrine\Common\Collections\ArrayCollection;
use SplObjectStorage;
use Throwable;

/**
 * Class HydratorProxy
 * @package Prokl\BitrixOrmBundle\Proxy
 */
class HydratorProxy
{
    use SourceRepoExtractorTrait;

    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     * @var CdbResultRepository|D7Repository|CacheProxy
     */
    protected $repository;

    /**
     * @var SplObjectStorage
     */
    protected $storage;

    /**
     * @var string
     */
    protected $itemType;

    /**
     * HydratorProxy constructor.
     */
    public function __construct()
    {
        $this->storage = new SplObjectStorage();
    }

    /**
     * @param HydratorInterface $hydrator
     */
    public function setHydrator(HydratorInterface $hydrator): void
    {
        $this->hydrator = $hydrator;
    }

    /**
     * @param CacheProxy|CdbResultRepository|D7Repository $repository
     */
    public function setRepository($repository): void
    {
        $this->repository = $repository;
    }

    /**
     * @return CacheProxy|CdbResultRepository|D7Repository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Обнаруживает создаваемые репозиторием объекты в возвращаемом результате и гидрирует их.
     *
     * @param $name
     * @param $arguments
     *
     * @throws Throwable
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $result = $this->getRepository()->$name(...$arguments);

        if ($result instanceof ArrayCollection) {
            foreach ($result as $item) {
                if (is_a($item, $this->getItemType())) {
                    $this->fill($item);
                }
            }
        } elseif (is_a($result, $this->getItemType())) {
            $this->fill($result);
        }

        return $result;
    }

    /**
     * @param BitrixArrayItemBase $item
     *
     * @throws Throwable
     * @return BitrixArrayItemBase
     */
    protected function fill(BitrixArrayItemBase $item): BitrixArrayItemBase
    {
        if (!$this->storage->contains($item)) {
            $this->storage->attach($item);

            try {
                $this->hydrator->fill($item);
            } catch (Throwable $e) {
                $this->storage->detach($item);
                throw $e;
            }
        }

        return $item;
    }

    /**
     * Возвращает тип создаваемого репозиторием объекта.
     *
     * @return string
     */
    private function getItemType(): string
    {
        if (is_null($this->itemType)) {
            if (
                $this->getRepository() instanceof CdbResultRepository
                || $this->getRepository() instanceof D7Repository
            ) {
                $this->itemType = $this->getRepository()->getFactory()->getItemType();
            } else {
                /**
                 * Откат к базовому типу на случай использования каких-то очень кастомных репозиториев в будущем.
                 */
                $this->itemType = BitrixArrayItemBase::class;
            }
        }

        return $this->itemType;
    }
}
