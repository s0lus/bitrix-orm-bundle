<?php

namespace Prokl\BitrixOrmBundle\Base\Factories;

use Prokl\BitrixOrmBundle\Base\Model\CatalogGroup;

class CatalogGroupFactory extends CdbResultItemFactory
{
    /**
     * @inheritdoc
     */
    public function getSelect(): array
    {
        return [
            'ID',
            'NAME',
            'BASE',
            'SORT',
            'XML_ID',
            'MODIFIED_BY',
            'CREATED_BY',
            'DATE_CREATE',
            'TIMESTAMP_X',
            'NAME_LANG',
            'CAN_ACCESS',
            'CAN_BUY',
            'CNT',
        ];

    }

    /**
     * @param array $data
     *
     * @return CatalogGroup
     */
    public function createItem(array $data): BitrixArrayItemInterface
    {
        return CatalogGroup::createFromArray($data);
    }

}
