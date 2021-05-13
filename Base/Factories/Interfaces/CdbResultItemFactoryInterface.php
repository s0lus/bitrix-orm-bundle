<?php

namespace Prokl\BitrixOrmBundle\Base\Factories\Interfaces;

use Prokl\BitrixOrmBundle\Base\Collection\CdbResultItemCollection;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\BitrixArrayItemInterface;
use CDBResult;

/**
 * Interface CdbResultItemFactoryInterface
 * @package Prokl\BitrixOrmBundle\Base\Factories\Interfaces
 */
interface CdbResultItemFactoryInterface extends ItemFactoryInterface
{
    /**
     * Создаёт объект сущности.
     *
     * @param array $data
     *
     * @return BitrixArrayItemInterface
     */
    public function createItem(array $data): BitrixArrayItemInterface;

    /**
     * Создаёт коллекцию.
     *
     * @param CDBResult $result
     *
     * @return CdbResultItemCollection
     */
    public function createCollection(CDBResult $result): CdbResultItemCollection;
}
