<?php

namespace Prokl\BitrixOrmBundle\Base;

use Prokl\BitrixOrmBundle\Base\Model\Interfaces\BitrixArrayItemInterface;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\HasIdInterface;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;

/**
 * @todo SplObjectStorage
 *
 * Class ObjectWatcher
 *
 * @package Prokl\BitrixOrmBundle\Base
 */
class ObjectWatcher
{
    /**
     * @var ArrayCollection
     */
    private $identityMap;

    public function __construct()
    {
        $this->clearAll();
    }

    /**
     * @param BitrixArrayItemInterface $item
     *
     * @return $this
     */
    public function add(BitrixArrayItemInterface $item): self
    {
        if ($item->getId() <= 0) {
            throw new InvalidArgumentException(
                sprintf(
                    'Item id must be positive and unique number among all `%s` items. '
                    . 'Probably, you are trying to add unsaved item under watching.',
                    get_class($item)
                )
            );
        }

        $this->identityMap->set($this->getItemKey($item), $item);

        return $this;
    }

    /**
     * @param string  $className Класс.
     * @param integer $id        ID.
     *
     * @return HasIdInterface|null
     */
    public function get(string $className, int $id): ?HasIdInterface
    {
        if (!$this->identityMap->offsetExists($this->getKey($className, $id))) {
            return null;
        }

        return $this->identityMap[$this->getKey($className, $id)];
    }

    /**
     * Возвращает только консистентный объект - т.е. либо уже существующий, либо тот же самый, но при этом добавляет
     * его под наблюдение.
     *
     * @param BitrixArrayItemInterface $item
     *
     * @return BitrixArrayItemInterface
     */
    public function getItem(BitrixArrayItemInterface $item): BitrixArrayItemInterface
    {
        $existingItem = $this->get(get_class($item), $item->getId());
        if ($existingItem instanceof BitrixArrayItemInterface) {
            return $existingItem;
        }

        $this->add($item);

        return $item;
    }

    /**
     * @param BitrixArrayItemInterface $item
     *
     * @return $this
     */
    public function removeItem(BitrixArrayItemInterface $item): self
    {
        $this->remove(get_class($item), $item->getId());

        return $this;
    }

    /**
     * @param string  $className Класс.
     * @param integer $id        ID.
     *
     * @return $this
     */
    public function remove(string $className, int $id): self
    {
        $itemKey = $this->getKey($className, $id);

        if ($this->identityMap->offsetExists($itemKey)) {
            $this->identityMap->remove($itemKey);
        }

        return $this;
    }

    /**
     * @param BitrixArrayItemInterface $item
     *
     * @return string
     */
    private function getItemKey(BitrixArrayItemInterface $item): string
    {
        return $this->getKey(get_class($item), $item->getId());
    }

    /**
     * @param string  $className Класс.
     * @param integer $id        ID.
     *
     * @return string
     */
    private function getKey(string $className, int $id): string
    {
        return $className . '\\' . $id;
    }

    /**
     * Очищает список всех наблюдаемых объектов.
     *
     * @return $this
     */
    public function clearAll(): self
    {
        $this->identityMap = new ArrayCollection();

        return $this;
    }
}
