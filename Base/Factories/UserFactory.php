<?php

namespace Prokl\BitrixOrmBundle\Base\Factories;


use Prokl\BitrixOrmBundle\Base\Model\Interfaces\BitrixArrayItemInterface;
use Prokl\BitrixOrmBundle\Base\Model\User;
use Prokl\BitrixOrmBundle\Base\Query\UserQuery;

class UserFactory extends CdbResultItemFactory
{
    /**
     * @param array $data
     *
     * @return User
     */
    public function createItem(array $data): BitrixArrayItemInterface
    {
        return User::createFromArray($data);
    }

    /**
     * @inheritDoc
     */
    public function getSelect(): array
    {
        /**
         * Выборка всех полей по-умолчанию, в том числе и пользовательских.
         */
        return UserQuery::SELECT_ALL;
    }

}
