<?php

namespace Prokl\BitrixOrmBundle\Table;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Entity;

/**
 * Class AbstractTable
 * @package Prokl\BitrixOrmBundle\Table
 */
abstract class AbstractTable extends DataManager
{
    /**
     * @param Entity $entity
     */
    public static function setEntity(Entity $entity): void
    {
        static::$entity['\\' . static::class] = $entity;
    }
}
