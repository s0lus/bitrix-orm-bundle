<?php

namespace Prokl\BitrixOrmBundle\Base\Factories;

use InvalidArgumentException;
use Prokl\BitrixOrmBundle\Base\Factories\Interfaces\ItemFactoryInterface;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\BitrixArrayItemInterface;
use Prokl\BitrixOrmBundle\Base\ObjectWatcher;

/**
 * Class ItemFactoryBase
 * @package Prokl\BitrixOrmBundle\Base\\Factories
 *
 *
 * method BitrixArrayItemInterface createItem(array $fields) Создает объект сущности(но в разных фабриках имеет разную
 *     сигнатуру).
 */
abstract class ItemFactoryBase implements ItemFactoryInterface
{
    /**
     * @var string
     */
    private $itemType;

    /**
     * @var ObjectWatcher
     */
    private $objectWatcher;

    public function __construct(ObjectWatcher $objectWatcher)
    {
        $this->objectWatcher = $objectWatcher;
    }

    /**
     * @return ObjectWatcher
     */
    public function getObjectWatcher(): ObjectWatcher
    {
        return $this->objectWatcher;
    }

    /**
     * Возвращает тип объекта, который обслуживается данным репозиторием.
     *
     * @return string
     */
    public function getItemType(): string
    {
        if (is_null($this->itemType)) {
            /**
             * Существование createItem() игнорируется,
             * т.к. у дочерних классов он точно есть,
             * но имеет несовместимые сигнатуры.
             */
            $this->itemType = get_class($this->createItem([]));
        }

        return $this->itemType;
    }

    /**
     * Всегда возвращает объект, создавая его из массива полей, или же просто возвращает объект без изменений, что
     * необходимо для случая, когда коллекция инициализирована из массива объектов.
     *
     * @param array|BitrixArrayItemInterface $arrayOrItem
     *
     * @return BitrixArrayItemInterface
     *
     * @throws InvalidArgumentException
     */
    protected function createItemSafely($arrayOrItem): BitrixArrayItemInterface
    {
        if (is_array($arrayOrItem)) {

            /**
             * Существование createItem() игнорируется,
             * т.к. у дочерних классов он точно есть,
             * но имеет несовместимые сигнатуры.
             */
            return $this->getObjectWatcher()->getItem($this->createItem($arrayOrItem));

        } elseif (is_object($arrayOrItem) && get_class($arrayOrItem) === $this->getItemType()) {

            return $this->getObjectWatcher()->getItem($arrayOrItem);

        }

        if (is_object($arrayOrItem)) {
            $wrongType = get_class($arrayOrItem);
        } else {
            $wrongType = gettype($arrayOrItem);
        }

        throw new InvalidArgumentException(
            sprintf(
                'Expected array or %s, but found %s instead.',
                $this->getItemType(),
                $wrongType
            )
        );
    }
}
