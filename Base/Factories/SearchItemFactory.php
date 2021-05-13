<?php

namespace Prokl\BitrixOrmBundle\Base\Factories;


use Prokl\BitrixOrmBundle\Base\Model\Interfaces\BitrixArrayItemInterface;
use Prokl\BitrixOrmBundle\Base\Model\SearchItem;

class SearchItemFactory extends CdbResultItemFactory
{
    /**
     * @param array $data
     *
     * @return SearchItem
     */
    public function createItem(array $data): BitrixArrayItemInterface
    {
        return SearchItem::createFromArray($data);
    }

    /**
     * @inheritDoc
     */
    public function getSelect(): array
    {
        /**
         * Модуль search не поддерживает установку select полей.
         */
        return [];
    }

}
