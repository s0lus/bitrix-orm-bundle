<?php

namespace Prokl\BitrixOrmBundle\Tools;

use Bitrix\Highloadblock\HighloadBlockTable;
use CUserFieldEnum;
use CUserTypeEntity;
use Exception;
use InvalidArgumentException;
use Prokl\BitrixOrmBundle\Tools\Exception\HlblockFieldNotFoundException;
use Prokl\BitrixOrmBundle\Tools\Exception\HlblockNotFoundException;
use Prokl\BitrixOrmBundle\Tools\Exception\UserFieldEnumNotFoundException;
use UnexpectedValueException;
use WebArch\BitrixCache\BitrixCache;
use function trim;

/**
 * Class HlblockTools
 * @package Prokl\BitrixOrmBundle\Tools
 */
class HlblockTools
{
    const ENTITY_PREFIX_HLBLOCK = 'HLBLOCK_';

    /**
     * @var array <name> => [ <fields> ]
     */
    protected static $hlBlockInfo = null;

    /**
     * @var array
     */
    protected static $hlBlockFieldInfo = null;

    /**
     * @var array
     */
    protected static $userFieldEnum = null;

    /**
     * Возвращает числовой ID HL-блока по его имени
     *
     * @param string $name
     *
     * @throws HLBlockNotFoundException
     * @throws Exception
     * @return integer
     */
    public static function getHLBlockIdByName(string $name): int
    {
        return (int)self::getHLBlockField($name, 'ID');
    }

    /**
     * Возвращает имя таблицы HL-блока по его имени
     *
     * @param string $name
     *
     * @throws HLBlockNotFoundException
     * @throws Exception
     * @return string
     */
    public static function getHLBlockTableNameByName(string $name): string
    {
        return (string)self::getHLBlockField($name, 'TABLE_NAME');
    }

    /**
     * Возвращает пользовательское поле HL-Блока
     *
     * @param string $hlBlockName Имя HL-блока
     * @param string $fieldName Имя поля
     *
     * @throws HLBlockFieldNotFoundException
     * @throws HLBlockNotFoundException
     * @throws Exception
     * @return array
     */
    public static function getHLBlockUserField(string $hlBlockName, string $fieldName): array
    {
        $hlBlockName = trim($hlBlockName);
        $fieldName = trim($fieldName);

        if ($hlBlockName === '' || $fieldName === '') {
            throw new InvalidArgumentException('HLBlock name and field name must be specified');
        }

        $entityId = self::ENTITY_PREFIX_HLBLOCK . self::getHLBlockIdByName($hlBlockName);

        //Перед тем, как ругаться, загрузить свежую информацию из БД
        if (!isset(self::getAllHLBlockFieldsInfo()[$entityId])) {
            self::getAllHLBlockFieldsInfo(true);
        }

        if (!isset(self::getAllHLBlockFieldsInfo()[$entityId])) {
            throw new HLBlockNotFoundException(
                sprintf(
                    'HLBlock `%s` not found',
                    $hlBlockName
                )
            );
        }

        if (isset(self::getAllHLBlockFieldsInfo()[$entityId][$fieldName])) {
            return self::getAllHLBlockFieldsInfo()[$entityId][$fieldName];
        }

        throw new HLBlockFieldNotFoundException(
            sprintf(
                'Field `%s` not found in HLBlock `%s`',
                $fieldName,
                $hlBlockName
            )
        );
    }

    /**
     * Возвращает id варианта списка для пользовательского свойства HL-блока.
     *
     * @param string $hlBlockName
     * @param string $fieldName
     * @param string $enumXmlId
     *
     * @throws InvalidArgumentException
     * @throws HLBlockFieldNotFoundException
     * @throws Exception
     * @throws HLBlockNotFoundException
     * @throws UnexpectedValueException
     * @throws UserFieldEnumNotFoundException
     * @return integer
     */
    public static function getUserFieldEnumId(string $hlBlockName, string $fieldName, string $enumXmlId): int
    {
        $enumXmlId = trim($enumXmlId);
        if ('' === $enumXmlId) {
            throw new InvalidArgumentException('Enum XML_ID must be specified');
        }

        $field = self::getHLBlockUserField($hlBlockName, $fieldName);

        //Перед тем, как ругаться, попытаться перезапросить информацию из БД
        if (!isset(self::getAllUserFieldEnum()[$field['ID']][$enumXmlId])) {
            self::getAllUserFieldEnum(true);
        }

        if (!isset(self::getAllUserFieldEnum()[$field['ID']][$enumXmlId])) {
            throw new UserFieldEnumNotFoundException(
                sprintf(
                    'Enum `%s` for field `%s` of HLBlock `%s` not found',
                    $enumXmlId,
                    $fieldName,
                    $hlBlockName
                )
            );
        }

        if (isset(self::getAllUserFieldEnum()[$field['ID']][$enumXmlId]['ID'])) {
            return (int)self::getAllUserFieldEnum()[$field['ID']][$enumXmlId]['ID'];
        }

        throw new UnexpectedValueException(
            sprintf(
                'Unable to get id of enum `%s` for field `%s` of HLBlock `%s`',
                $enumXmlId,
                $fieldName,
                $hlBlockName
            )
        );
    }

    /**
     * Возвращает пункты списка для поля типа "Список".
     *
     * @param string $hlBlockName
     * @param string $fieldName
     *
     * @throws HlblockFieldNotFoundException
     * @throws HlblockNotFoundException
     * @throws UserFieldEnumNotFoundException
     * @throws Exception
     * @return array [
     *                  [
     *                      'ID' => 'int|string',
     *                      'USER_FIELD_ID' => 'int|string',
     *                      'VALUE' => 'string',
     *                      'DEF' => 'Y|N',
     *                      'SORT' => 'int|string',
     *                      'XML_ID' => 'string',
     *                  ]
     *              ]
     */
    public static function getUserFieldEnumList(string $hlBlockName, string $fieldName): array
    {
        $field = self::getHLBlockUserField($hlBlockName, $fieldName);

        //Перед тем, как ругаться, попытаться перезапросить информацию из БД
        if (!isset(self::getAllUserFieldEnum()[$field['ID']])) {
            self::getAllUserFieldEnum(true);
        }

        if (!isset(self::getAllUserFieldEnum()[$field['ID']])) {
            throw new UserFieldEnumNotFoundException(
                sprintf(
                    'Enum list for field `%s` of HLBlock `%s` not found',
                    $fieldName,
                    $hlBlockName
                )
            );
        }

        return self::getAllUserFieldEnum()[$field['ID']];
    }

    /**
     * Возвращает пункт списка для поля типа "Список" по числовому id пункта.
     *
     * @param string $hlBlockName
     * @param string $fieldName
     * @param integer $id
     *
     * @throws HlblockFieldNotFoundException
     * @throws HlblockNotFoundException
     * @throws UserFieldEnumNotFoundException
     * @return array [
     *                  'ID' => 'int|string',
     *                  'USER_FIELD_ID' => 'int|string',
     *                  'VALUE' => 'string',
     *                  'DEF' => 'Y|N',
     *                  'SORT' => 'int|string',
     *                  'XML_ID' => 'string',
     *               ]
     */
    public static function getUserFieldEnumById(string $hlBlockName, string $fieldName, int $id): array
    {
        $list = HlblockTools::getUserFieldEnumList($hlBlockName, $fieldName);

        foreach ($list as $enumItem) {
            if ($id === $enumItem['ID']) {
                return $enumItem;
            }
        }

        throw new UserFieldEnumNotFoundException(
            sprintf(
                'Enum #%s for field `%s` of HLBlock `%s` not found',
                $id,
                $fieldName,
                $hlBlockName
            )
        );
    }

    /**
     * @param boolean $clearCache
     *
     * @throws Exception
     * @return array
     */
    protected static function getAllUserFieldEnum(bool $clearCache = false): array
    {
        if (null === self::$userFieldEnum || true === $clearCache) {
            $closure = function () {
                $userFieldEnum = [];
                $dbEnumList = (new CUserFieldEnum())->GetList();
                while ($enumValue = $dbEnumList->Fetch()) {

                    $userFieldId = (int)$enumValue['USER_FIELD_ID'];
                    $xmlId = (string)$enumValue['XML_ID'];
                    $userFieldEnum[$userFieldId][$xmlId] = $enumValue;

                }

                return $userFieldEnum;
            };

            self::$userFieldEnum = (new BitrixCache())->setId(__METHOD__)
                                                      ->setTime(86400)
                                                      ->setClearCache($clearCache)
                                                      ->callback($closure);

        }

        return self::$userFieldEnum;
    }

    /**
     * @param boolean $clearCache
     *
     * @throws Exception
     * @return array
     */
    protected static function getAllHLBlockFieldsInfo(bool $clearCache = false): array
    {
        if (null === self::$hlBlockFieldInfo || true === $clearCache) {

            $closure = function () {
                $hlBlockFieldInfo = [];
                $fieldList = CUserTypeEntity::GetList();
                while ($field = $fieldList->Fetch()) {
                    $hlBlockFieldInfo[$field['ENTITY_ID']][$field['FIELD_NAME']] = $field;
                }

                return $hlBlockFieldInfo;
            };

            self::$hlBlockFieldInfo = (new BitrixCache())->setId(__METHOD__)
                                                         ->setTime(86400)
                                                         ->setClearCache($clearCache)
                                                         ->callback($closure);
        }

        return self::$hlBlockFieldInfo;
    }

    /**
     * @param boolean $clearCache
     *
     * @throws Exception
     * @return array
     */
    protected static function getAllHLBlockInfo(bool $clearCache = false): array
    {
        if (null === self::$hlBlockInfo || true === $clearCache) {

            $closure = function () {
                $hlBlockInfo = [];
                $hlBlockList = HighloadBlockTable::query()
                                                 ->setSelect(['*'])
                                                 ->exec();
                while ($hlBlockFields = $hlBlockList->fetch()) {
                    $hlBlockInfo[$hlBlockFields['NAME']] = $hlBlockFields;
                }

                return $hlBlockInfo;
            };

            self::$hlBlockInfo = (new BitrixCache())->setId(__METHOD__)
                                                    ->setTime(86400)
                                                    ->setClearCache($clearCache)
                                                    ->callback($closure);

        }

        return self::$hlBlockInfo;
    }

    /**
     * @param string $hlBlockName
     * @param string $field
     *
     * @throws InvalidArgumentException
     * @throws HLBlockNotFoundException
     * @throws Exception
     * @return mixed
     *
     */
    public static function getHLBlockField(string $hlBlockName, string $field)
    {
        $hlBlockName = trim($hlBlockName);
        $field = trim($field);

        if ($hlBlockName === '' || $field === '') {
            throw new InvalidArgumentException('HLBlock name and field must be specified');
        }

        //Перед тем, как ругаться, запросить свежую информацию из БД
        if (!isset(self::getAllHLBlockInfo()[$hlBlockName])) {
            self::getAllHLBlockInfo(true);
        }

        if (!isset(self::getAllHLBlockInfo()[$hlBlockName])) {
            throw new HLBlockNotFoundException(
                sprintf(
                    'HLBlock `%s` not found',
                    $hlBlockName
                )
            );
        }

        if (isset(self::getAllHLBlockInfo()[$hlBlockName][$field])) {
            return self::getAllHLBlockInfo()[$hlBlockName][$field];
        }

        throw new InvalidArgumentException(
            sprintf(
                'Field `%s` not found in hlblock `%s`',
                $field,
                $hlBlockName
            )
        );
    }
}
