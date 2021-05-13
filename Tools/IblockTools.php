<?php

namespace Prokl\BitrixOrmBundle\Tools;

use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Iblock\TypeTable;
use Exception;
use InvalidArgumentException;
use Prokl\BitrixOrmBundle\Tools\Exception\IblockNotFoundException;
use Prokl\BitrixOrmBundle\Tools\Exception\IblockPropertyNotFoundException;
use RuntimeException;
use WebArch\BitrixCache\BitrixCache;

/**
 * Class IblockTools
 * @package Prokl\BitrixOrmBundle\Tools
 */
class IblockTools
{
    const QUERY_CACHE_TTL = 3600;

    const PROPERTY_VALUES_KEY = 'PROPERTY_VALUES';

    const IBLOCK_ID_KEY = 'IBLOCK_ID';

    /**
     * @var array|null
     */
    private static $iblockInfo;

    /**
     * @var array|null <iblock_id> => <property_code> => <array_of_property_fields>
     */
    private static $propertyInfo;

    /**
     * Возвращает id инфоблока по его типу и символьному коду
     *
     * @param string $type
     * @param string $code
     *
     * @return integer
     * @throws IblockNotFoundException
     * @throws Exception
     */
    public static function getIblockId(string $type, string $code): int
    {
        return (int)self::getIblockField($type, $code, 'ID');
    }

    /**
     * Возвращает xml id инфоблока по его типу и символьному коду
     *
     * @param string $type
     * @param string $code
     *
     * @return string
     * @throws IblockNotFoundException
     * @throws Exception
     */
    public static function getIblockXmlId(string $type, string $code): string
    {
        return trim(self::getIblockField($type, $code, 'XML_ID'));
    }

    /**
     * @param string $type
     * @param string $code
     * @param string $field
     *
     * @return mixed
     * @throws IblockNotFoundException
     * @throws Exception
     */
    public static function getIblockField(string $type, string $code, string $field)
    {
        $type = \trim($type);
        $code = \trim($code);

        if ($type === '' || $code === '') {
            throw new InvalidArgumentException('Iblock type and code must be specified');
        }

        //Перед тем, как ругаться, что инфоблок не найден, попытаться перезапросить информацию из базы
        if (!isset(self::getAllIblockInfo()[$type][$code])) {
            self::getAllIblockInfo(true);
        }

        if (!isset(self::getAllIblockInfo()[$type][$code])) {
            throw new IblockNotFoundException(
                sprintf(
                    'Iblock `%s\%s` not found',
                    $type,
                    $code
                )
            );
        }

        if (isset(self::getAllIblockInfo()[$type][$code][$field])) {
            return (int)self::getAllIblockInfo()[$type][$code][$field];
        }

        throw new InvalidArgumentException(
            sprintf(
                'Field `%s` not found in iblock %s\%s',
                $field,
                $type,
                $code
            )
        );
    }

    /**
     * Возвращает краткую информацию обо всех инфоблоках в виде многомерного массива.
     *
     * @param boolean $clearCache
     *
     * @return array <iblock type> => <iblock code> => array of iblock fields
     * @throws Exception
     */
    protected static function getAllIblockInfo(bool $clearCache = false): array
    {
        if (null === self::$iblockInfo || true === $clearCache) {
            $closure = function () : array {
                $iblockList = IblockTable::query()
                                         ->setSelect(
                                             ['ID', 'IBLOCK_TYPE_ID', 'CODE', 'XML_ID']
                                         )
                                         ->exec();
                $iblockInfo = [];
                while ($iblock = $iblockList->fetch()) {
                    $iblockInfo[$iblock['IBLOCK_TYPE_ID']][$iblock['CODE']] = $iblock;
                }

                return $iblockInfo;
            };

            self::$iblockInfo = (new BitrixCache())->withId(__METHOD__)
                                                   ->withTime(86400)
                                                   ->withClearCache($clearCache)
                                                   ->resultOf($closure);
        }

        return self::$iblockInfo;
    }

    /**
     * Возвращает id свойства инфоблока по символьному коду
     *
     * @param integer $iblockId
     * @param string  $code
     *
     * @return integer
     * @throws IblockPropertyNotFoundException
     * @throws Exception
     */
    public static function getPropertyId(int $iblockId, string $code): int
    {
        return (int)self::getPropertyField($iblockId, $code, 'ID');
    }

    /**
     * Возвращает символьный код свойства по его числовому id.
     *
     * @param integer $iblockId   ID инфоблока.
     * @param integer $propertyId ID свойства инфоблока.
     *
     * @return string
     * @throws Exception
     * @throws IblockPropertyNotFoundException
     */
    public static function getPropertyCode(int $iblockId, int $propertyId): string
    {
        $propertyNotFoundMessage = sprintf(
            'Iblock property #%d for iblock #%d not found',
            $propertyId,
            $iblockId
        );
        $allPropertyInfo = self::getAllPropertyInfo();

        if (!array_key_exists($iblockId, $allPropertyInfo)) {
            throw new IblockPropertyNotFoundException(
                $propertyNotFoundMessage
            );
        }

        foreach ($allPropertyInfo[$iblockId] as $propertyCode => $propertyFields) {
            if (isset($propertyFields['ID']) && $propertyFields['ID'] === $propertyId) {
                return trim($propertyCode);
            }
        }

        throw new IblockPropertyNotFoundException(
            $propertyNotFoundMessage
        );
    }

    /**
     * @param integer $iblockId
     * @param string  $propertyCode
     * @param string  $field
     *
     * @return mixed
     * @throws IblockPropertyNotFoundException
     * @throws Exception
     */
    public static function getPropertyField(int $iblockId, string $propertyCode, string $field)
    {
        $iblockId = (int)$iblockId;
        $propertyCode = trim($propertyCode);

        if ($iblockId <= 0 || $propertyCode === '') {
            throw new InvalidArgumentException('Iblock id and property code must be specified');
        }

        //Перед тем, как ругаться, попытаться перезапросить информацию из базы
        if (!isset(self::getAllPropertyInfo()[$iblockId][$propertyCode])) {
            self::getAllPropertyInfo(true);
        }

        if (!isset(self::getAllPropertyInfo()[$iblockId][$propertyCode])) {
            throw new IblockPropertyNotFoundException(
                sprintf(
                    'Iblock property `%s` for iblock #%d not found',
                    $propertyCode,
                    $iblockId
                )
            );
        }

        if (isset(self::getAllPropertyInfo()[$iblockId][$propertyCode][$field])) {
            return self::getAllPropertyInfo()[$iblockId][$propertyCode][$field];
        }

        throw new InvalidArgumentException(
            sprintf(
                'Field `%s` not found in property `%s` for iblock #%d',
                $field,
                $propertyCode,
                $iblockId
            )
        );

    }

    /**
     * Возвращает информацию обо всех свойствах в виде многомерного массива.
     *
     * @param boolean $clearCache
     *
     * @return array <iblock_id> => <property_code> => <array_of_property_fields>
     * @throws Exception
     */
    protected static function getAllPropertyInfo(bool $clearCache = false): array
    {
        if (null === self::$propertyInfo || true === $clearCache) {

            $closure = function () : array {
                $propertyInfo = [];
                $propertyList = PropertyTable::query()
                                             ->setSelect(['*'])
                                             ->setFilter([])
                                             ->exec();

                while ($propertyFields = $propertyList->fetch()) {
                    $iblockId = (int)$propertyFields['IBLOCK_ID'];
                    $propertyCode = (string)$propertyFields['CODE'];
                    $propertyInfo[$iblockId][$propertyCode] = $propertyFields;
                }

                return $propertyInfo;
            };

            self::$propertyInfo = (new BitrixCache())->withId(__METHOD__)
                                                     ->withTime(86400)
                                                     ->withClearCache($clearCache)
                                                     ->resultOf($closure);
        }

        return self::$propertyInfo;
    }

    /**
     * Проверка существования типа инфоблоков
     *
     * @param string $typeId
     *
     * @return boolean
     */
    public static function isIblockTypeExists($typeId): bool
    {
        $typeId = trim($typeId);
        if ($typeId === '') {
            throw new InvalidArgumentException('Iblock type id must be specified');
        }

        return 1 === TypeTable::query()
                              ->setCacheTtl(self::QUERY_CACHE_TTL)
                              ->setSelect(['ID'])
                              ->setFilter(['=ID' => $typeId])
                              ->setLimit(1)
                              ->exec()
                              ->getSelectedRowsCount();
    }

    /**
     * Конвертирует массив с полями и свойствами элемента инфоблока, когда свойства хранятся в ключе 'PROPERTY_VALUES',
     * так, как будто данные получены из CIBlockElement::GetList с указанием свойств в параметре $arSelect.
     *
     * @internal Это вспомогательный метод, облегчающий создание объектов при помощи пакета adv/bitrix-orm в
     *     обработчиках событий iblock:OnBeforeIBlockElementAdd и iblock:OnBeforeIBlockElementUpdate
     *
     * @param array $fields Массив полей и свойств элемента инфоблока, в котором значения свойств хранятся в ключе
     *     `PROPERTY_VALUES`, а в `IBLOCK_ID` хранится числовой id инфоблока.
     *
     * @return array Массив, в котором значения свойств хранятся под ключами `PROPERTY_<code>_VALUE`, описания значений
     *     свойств хранятся `PROPERTY_<code>_DESCRIPTION`, а ключ `PROPERTY_VALUES` удалён.
     * @throws Exception
     * @throws IblockPropertyNotFoundException
     */
    public static function convertPropertyValuesKeyInIblockElementFields(array $fields): array
    {
        if (!array_key_exists(self::IBLOCK_ID_KEY, $fields)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Key `%s` does not exist in fields',
                    self::IBLOCK_ID_KEY
                )
            );
        }

        if ($fields[self::IBLOCK_ID_KEY] <= 0 || !is_int($fields[self::IBLOCK_ID_KEY])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Value in key `%s` must be positive integer value.',
                    self::IBLOCK_ID_KEY
                )
            );
        }

        if (!array_key_exists(self::PROPERTY_VALUES_KEY, $fields)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Key `%s` does not exist in fields',
                    self::PROPERTY_VALUES_KEY
                )
            );
        }

        if (!is_array($fields[self::PROPERTY_VALUES_KEY])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Value in key `%s` must be of array type',
                    self::PROPERTY_VALUES_KEY
                )
            );
        }

        /** @psalm-suppress RedundantCast */
        $iblockId = (int)$fields[self::IBLOCK_ID_KEY];
        $propertyValues = [];

        foreach ($fields[self::PROPERTY_VALUES_KEY] as $propertyId => $propertyComplexValue) {

            $propertyCode = self::getPropertyCode($iblockId, (int)$propertyId);
            $isMultiple = self::getPropertyField($iblockId, $propertyCode, 'MULTIPLE') === 'Y';

            /**
             * Если не множественное свойство, то должно быть одно единственное значение.
             */
            if (!$isMultiple && 1 === count($propertyComplexValue)) {

                foreach ($propertyComplexValue as $valueId => $singleComplexValue) {
                    foreach ($singleComplexValue as $key => $data) {
                        $resultKey = sprintf(
                            'PROPERTY_%s_%s',
                            $propertyCode,
                            $key
                        );
                        $propertyValues[$resultKey] = $data;
                    }
                }

            } elseif ($isMultiple) {

                foreach ($propertyComplexValue as $valueId => $singleComplexValue) {
                    foreach ($singleComplexValue as $key => $data) {
                        $resultKey = sprintf(
                            'PROPERTY_%s_%s',
                            $propertyCode,
                            $key
                        );
                        $propertyValues[$resultKey][$valueId] = $data;
                    }
                }

            } else {
                throw new RuntimeException(
                    sprintf(
                        'Error converting property `%s` of iblock #%d',
                        $propertyCode,
                        $iblockId

                    )
                );
            }
        }

        $result = array_merge($fields, $propertyValues);
        unset($result[self::PROPERTY_VALUES_KEY]);

        return $result;
    }
}
