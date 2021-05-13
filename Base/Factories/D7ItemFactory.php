<?php

namespace Prokl\BitrixOrmBundle\Base\Factories;

use Bitrix\Main\DB\Result;
use Generator;
use Prokl\BitrixOrmBundle\Base\Collection\D7ItemCollection;
use Prokl\BitrixOrmBundle\Base\Factories\Interfaces\D7ItemFactoryInterface;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\BitrixArrayItemInterface;

abstract class D7ItemFactory extends ItemFactoryBase implements D7ItemFactoryInterface
{
    /**
     * @param Result $result
     *
     * @return D7ItemCollection
     */
    public function createCollection(Result $result): D7ItemCollection
    {
        return $this->populateCollection($result, new D7ItemCollection());
    }

    /**
     * Извлекает данные из ресурса БД.
     *
     * @inheritdoc Метод должен обязательно производить проверку: если массив, то создавать объект или же просто
     *     проверять тип объекта и возвращать его.
     *
     * @return Generator
     */
    protected function fetchItem(Result $result): Generator
    {
        while ($fields = $result->fetch()) {
            yield $this->createItemSafely($fields);
        }
    }

    /**
     * @param Result $result
     * @param $collection
     *
     * @return D7ItemCollection
     */
    protected function populateCollection(Result $result, D7ItemCollection $collection): D7ItemCollection
    {
        foreach ($this->fetchItem($result) as $item) {
            if ($item instanceof BitrixArrayItemInterface) {
                $collection->set($item->getId(), $item);
            }
        }

        return $collection;
    }

}
