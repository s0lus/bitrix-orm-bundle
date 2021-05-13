<?php

namespace Prokl\BitrixOrmBundle\Base\Repository;

use Prokl\BitrixOrmBundle\Base\Exception\InvalidArgumentException;
use Prokl\BitrixOrmBundle\Base\Exception\ItemNotFoundException;
use Prokl\BitrixOrmBundle\Base\Factories\D7ItemFactory;
use Prokl\BitrixOrmBundle\Base\Factories\UserD7Factory;
use Prokl\BitrixOrmBundle\Base\Model\User;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;

class UserD7Repository extends D7Repository
{
    public function __construct(DataManager $dataManager, UserD7Factory $factory)
    {
        parent::__construct($dataManager, $factory);
    }

    /**
     * @return UserD7Factory
     */
    public function getFactory(): D7ItemFactory
    {
        return parent::getFactory();
    }

    /**
     * @param string $phone
     *
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ItemNotFoundException
     * @return User
     */
    public function findByPersonalPhone(string $phone): User
    {
        if (trim($phone) === '') {
            throw new InvalidArgumentException(
                'Personal phone must be specified.'
            );
        }

        $fields = $this->createQuery()
                       ->setSelect($this->getFactory()->getSelect())
                       ->setFilter(['=PERSONAL_PHONE' => $phone])
                       ->setLimit(1)
                       ->exec()
                       ->fetch();
        if (false === $fields || !is_array($fields)) {
            throw new ItemNotFoundException(
                sprintf(
                    'User having personal phone "%s" is not found.',
                    $phone
                )
            );
        }

        return $this->getFactory()->createItem($fields);
    }
}
