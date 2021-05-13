<?php

namespace Prokl\BitrixOrmBundle\Tools;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\Query;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Exception;
use Prokl\BitrixOrmBundle\Tools\Exception\HlblockNotFoundException;
use LogicException;
use RuntimeException;
use function is_array;
use function is_object;
use function is_string;

/**
 * Class HlblockFactory
 * @package Prokl\BitrixOrmBundle\Tools
 */
class HlblockFactory
{
    const /** @noinspection SummerTimeUnsafeTimeManipulationInspection */
        QUERY_CACHE_TTL = 3600 * 24;

    /**
     * Возвращает скомпилированную сущность HL-блока по имени его сущности.
     *
     * @param string $hlBlockName
     *
     * @throws HlblockNotFoundException
     * @return DataManager
     */
    public static function createTableObject($hlBlockName): DataManager
    {
        try {
            return self::doCreateTableObject(['=NAME' => $hlBlockName]);
        } catch (Exception $exception) {
            throw new HlblockNotFoundException(
                sprintf(
                    'HLBlock with name %s is not found.',
                    $hlBlockName
                ),
                0,
                $exception
            );
        }
    }

    /**
     * Возвращает скомпилированную сущность HL-блока по имени его таблицы в базе данных.
     *
     * @param string $tableName
     *
     * @throws HlblockNotFoundException
     * @return DataManager
     */
    public static function createTableObjectByTable($tableName): DataManager
    {
        try {
            return self::doCreateTableObject(['=TABLE_NAME' => $tableName]);
        } catch (Exception $exception) {
            throw new HlblockNotFoundException(
                sprintf(
                    'HLBlock with table %s is not found.',
                    $tableName
                ),
                0,
                $exception
            );
        }
    }

    /**
     * Возвращает скомпилированную сущность HL-блока по заданному фильтру, но фильтр должен в итоге находить один
     * HL-блок.
     *
     * @param array $filter
     *
     * @throws ArgumentException
     * @throws LoaderException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @return DataManager
     */
    private static function doCreateTableObject(array $filter): DataManager
    {
        Loader::includeModule('highloadblock');

        $result = (new Query(HighloadBlockTable::getEntity()))
            ->setFilter($filter)
            ->setSelect(['*'])
            ->setCacheTtl(self::QUERY_CACHE_TTL)
            ->exec();

        if ($result->getSelectedRowsCount() > 1) {
            throw new LogicException('More than one HLBlock are found. Wrong filter?');
        }

        $hlBlockFields = $result->fetch();

        if (!is_array($hlBlockFields)) {
            throw new RuntimeException(
                sprintf(
                    'HLBlock is not found. Used filter: %s',
                    var_export($filter, true)
                )
            );
        }

        $dataManager = HighloadBlockTable::compileEntity($hlBlockFields)->getDataClass();

        if (is_object($dataManager)) {
            return $dataManager;
        }

        if (is_string($dataManager)) {
            return new $dataManager;
        }

        throw new RuntimeException('Error HLBlock compilation.');
    }
}
