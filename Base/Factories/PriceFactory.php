<?php

namespace Prokl\BitrixOrmBundle\Base\Factories;

use Prokl\BitrixOrmBundle\Base\Model\D7Item;
use Prokl\BitrixOrmBundle\Base\Model\Price;

class PriceFactory extends D7ItemFactory
{
    /**
     * @inheritDoc
     */
    public function getSelect(): array
    {
        return ['*'];
    }

    /**
     * @param array $fields
     *
     * @return Price
     */
    public function createItem(array $fields): D7Item
    {
        return Price::createFromArray($fields);
    }
}
