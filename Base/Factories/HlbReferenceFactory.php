<?php

namespace Prokl\BitrixOrmBundle\Base\Factories;

use Prokl\BitrixOrmBundle\Base\Model\D7Item;
use Prokl\BitrixOrmBundle\Base\Model\HlbReferenceItem;

class HlbReferenceFactory extends D7ItemFactory
{
    /**
     * @inheritdoc
     */
    public function getSelect(): array
    {
        return ['*'];
    }

    /**
     * @param array $fields
     *
     * @return D7Item
     */
    public function createItem(array $fields): D7Item
    {
        return HlbReferenceItem::createFromArray($fields);
    }

}
