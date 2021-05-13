<?php

namespace Prokl\BitrixOrmBundle\Base\Factories;

use Prokl\BitrixOrmBundle\Base\Model\D7Item;
use Prokl\BitrixOrmBundle\Base\Model\User;

class UserD7Factory extends D7ItemFactory
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
     * @return User
     */
    public function createItem(array $fields): D7Item
    {
        return User::createFromArray($fields);
    }
}
