<?php

namespace Prokl\BitrixOrmBundle\Factory;

use Bitrix\Main\Loader;
use Prokl\BitrixOrmBundle\Tools\Convertors\BitrixBool;
use Prokl\BitrixOrmBundle\Tools\HlblockFactory as HlFactory;
use Prokl\BitrixOrmBundle\Table\AbstractHlblockTable;
use Prokl\BitrixOrmBundle\Table\AbstractTable;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Entity\DataManager as HlDataManager;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Entity;
use Bitrix\Main\ORM\Query\Query;
use Exception;
use LogicException;
use ReflectionProperty;
use WebArch\BitrixCache\BitrixCache;
use function is_array;

Loader::includeModule('highloadblock');

/**
 * Class HlblockFactory
 * @package Prokl\BitrixOrmBundle\Factory
 */
class HlblockFactory
{
    /**
     * @param string $hlBlockName
     *
     * @return HlDataManager
     * @throws Exception
     */
    public static function createTableObject(string $hlBlockName): HlDataManager
    {
        return HlFactory::createTableObject($hlBlockName);
    }

    /**
     *  Хак, который инициализирует HL-блоки в 100 раз быстрее
     *
     * @param string $hlBlockName
     *
     * @return HlDataManager
     * @throws Exception
     */
    public static function createTableObjectWithCache(string $hlBlockName): HlDataManager
    {
        $callback = function () use ($hlBlockName) : array {
            $query  = HighloadBlockTable::query();
            $result = $query->setFilter(['NAME' => $hlBlockName])
                            ->setSelect(['*'])
                            ->setCacheTtl(HlFactory::QUERY_CACHE_TTL)
                            ->exec();

            if ($result->getSelectedRowsCount() > 1) {
                throw new LogicException('Неверный фильтр: найдено несколько HLBlock.');
            }

            $hlblock = $result->fetch();

            if (!is_array($hlblock)) {
                throw new Exception('HLBlock не найден.');
            }

            $entity = HighloadBlockTable::compileEntity($hlblock);

            $multipleFields = [];
            global $USER_FIELD_MANAGER;
            $fields = $USER_FIELD_MANAGER->GetUserFields('HLBLOCK_' . $hlblock['ID']);
            foreach ($fields as $field) {
                if (BitrixBool::TRUE !== $field['MULTIPLE']) {
                    continue;
                }

                $multipleFields[$field['FIELD_NAME']] = [
                    'field'  => $field,
                    'entity' => Entity::getInstance(HighloadBlockTable::getUtmEntityClassName($entity, $field)),
                ];
            }

            return [
                'hlblock'        => $hlblock,
                'entity'         => $entity,
                'multipleFields' => $multipleFields,
            ];
        };

        $data = (new BitrixCache())->setId('HlblockFactory::createTableObjectWithCache' . '|' . $hlBlockName)
                                   ->callback($callback);
        /** @var array $hlblock */
        $hlblock = $data['hlblock'];
        /** @var Entity $entity */
        $entity         = $data['entity'];
        $multipleFields = $data['multipleFields'];
        $result = static::getEntityClass($hlblock);

        if ($result instanceof AbstractHlblockTable) {
            $result::setEntity($entity);
        }

        $classNameProp = new ReflectionProperty(Entity::class, 'className');
        $classNameProp->setAccessible(true);
        $className = $classNameProp->getValue($entity);
        $instancesProp = new ReflectionProperty(Entity::class, 'instances');
        $instancesProp->setAccessible(true);
        $instances = $instancesProp->getValue();
        /**
         * Сохранение полученной из кеша корректной $entity в Entity::$instances,
         * иначе будет ошибка "Bitrix\Main\SystemException: Unknown field definition"
         */
        if (!is_array($instances) || !array_key_exists($className, $instances)) {
            $instances[$className] = $entity;
        }
        foreach ($multipleFields as $multipleField) {
            $field     = $multipleField['field'];
            $utmEntity = $multipleField['entity'];

            $className = HighloadBlockTable::getUtmEntityClassName($entity, $field) . 'Table';
            $utmClass  = static::getUtmEntityClass(
                $className,
                HighloadBlockTable::getMultipleValueTableName($hlblock, $field)
            );

            $instances['\\' . $className] = $utmEntity;

            if ($utmClass instanceof AbstractTable) {
                $utmClass::setEntity($utmEntity);
            }
        }
        $instancesProp->setValue(null, $instances);

        return $result;
    }

    /**
     * @param array $fields Поля.
     *
     * @return HlDataManager
     */
    protected static function getEntityClass(array $fields): HlDataManager
    {
        $className = $fields['NAME'] . 'Table';

        if (!class_exists($className)) {
            $map = [
                'ID' => [
                    'data_type'    => 'integer',
                    'primary'      => true,
                    'autocomplete' => true,
                ],
            ];

            $eval = '
                class ' . $className . ' extends ' . AbstractHlblockTable::class . '
                {
                    public static function getTableName()
                    {
                        return ' . var_export($fields['TABLE_NAME'], true) . ';
                    }

                    public static function getMap()
                    {
                        return ' . var_export($map, true) . ';
                    }

                    public static function getHighloadBlock()
                    {
                        return ' . var_export($fields, true) . ';
                    }
                }
            ';

            eval($eval);
        }

        return new $className();
    }

    /**
     * @param string $className Название класса.
     * @param string $tableName Название таблицы.
     *
     * @return mixed
     */
    protected static function getUtmEntityClass(string $className, string $tableName): DataManager
    {
        if (!class_exists($className)) {
            $eval = '
                class ' . $className . ' extends ' . DataManager::class . '
                {
                    public static function getTableName()
                    {
                        return ' . var_export($tableName, true) . ';
                    }
                }
            ';

            eval($eval);
        }

        return new $className();
    }
}
