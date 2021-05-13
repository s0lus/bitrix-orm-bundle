<?php

namespace Prokl\BitrixOrmBundle\Base\Factories\Interfaces;

use Bitrix\Main\DB\Result;
use Prokl\BitrixOrmBundle\Base\Collection\D7ItemCollection;
use Prokl\BitrixOrmBundle\Base\Model\D7Item;

interface D7ItemFactoryInterface extends ItemFactoryInterface
{
    /**
     * Создаёт объект сущности.
     *
     * @param array $fields
     *
     * @return D7Item
     */
    public function createItem(array $fields): D7Item;

    /**
     * Создаёт коллекцию.
     *
     * @param Result $result
     *
     * @return D7ItemCollection
     */
    public function createCollection(Result $result): D7ItemCollection;
}
