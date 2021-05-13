<?php

namespace Prokl\BitrixOrmBundle\Table;

use Bitrix\Highloadblock\DataManager;
use Bitrix\Main\ORM\Entity;

/**
 * Class AbstractHlblockTable
 * @package Prokl\BitrixOrmBundle\Table
 */
abstract class AbstractHlblockTable extends DataManager
{
    /**
     * @param Entity $entity
     */
    public static function setEntity(Entity $entity): void
    {
        static::$entity['\\' . static::class] = $entity;
    }
}
