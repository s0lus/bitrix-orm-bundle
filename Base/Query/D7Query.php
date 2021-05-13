<?php

namespace Prokl\BitrixOrmBundle\Base\Query;

use Prokl\BitrixOrmBundle\Base\Model\D7Item;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\DB\Result;
use Bitrix\Main\Entity\AddResult;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\DeleteResult;
use Bitrix\Main\Entity\Field;
use Bitrix\Main\Entity\UpdateResult;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Exception;

/**
 * Class D7Query
 * @package Prokl\BitrixOrmBundle\Base\Query
 */
class D7Query extends QueryBase
{
    /**
     * @var DataManager
     */
    private $dataManager;

    /**
     * @var array[]
     */
    protected $runtimeFields = [];

    /**
     * D7Query constructor.
     *
     * @param DataManager $dataManager
     */
    public function __construct(DataManager $dataManager)
    {
        $this->dataManager = $dataManager;
    }

    /**
     * @return array[]
     */
    public function getRuntimeFields(): array
    {
        return $this->runtimeFields;
    }

    /**
     * @param array[]|Field[] $runtimeFields
     *
     * @return $this
     */
    public function setRuntimeFields(array $runtimeFields): self
    {
        $this->runtimeFields = $runtimeFields;

        return $this;
    }

    /**
     * @param Field $field
     *
     * @return $this
     */
    public function registerRuntimeField(Field $field): self
    {
        $this->runtimeFields[$field->getName()] = $field;

        return $this;
    }

    /**
     * @throws ArgumentException | ObjectPropertyException | SystemException
     *
     * @return Result
     */
    public function exec(): Result
    {
        $query = $this->dataManager::query();

        if (count($this->getRuntimeFields()) > 0) {
            foreach ($this->getRuntimeFields() as $name => $runtimeField) {
                $query->registerRuntimeField($name, $runtimeField);
            }
        }

        return $query->setSelect($this->getSelect())
                     ->setFilter($this->getFilter())
                     ->setOrder($this->getOrder())
                     ->setLimit($this->getLimit())
                     ->setOffset($this->getOffset())
                     ->setGroup($this->getGroup())
                     ->exec();
    }

    /**
     * @param D7Item $item
     *
     * @throws Exception
     * @return AddResult
     */
    public function add(D7Item $item): AddResult
    {
        /**
         * Пустые поля(=== null), участвующие в primary, должны быть отброшены, т.к. highloadblock >= 20.0 версии
         * выполняет два запроса при добавлении: INSERT, UPDATE. Запрос UPDATE ломает поля первичного ключа.
         * Например, SET `ID` = NULL вынуждает базу данных установить ID = 0.
         *
         * Битрикс < 20 версии делает только один INSERT. Отсутствие первичного ключа по ID приведёт к срабатыванию
         * auto_increment.
         *
         * В остальных же случаях обязанность передачи первичного ключа лежит на клиенте, и такие поля не будут
         * отброшены, т.к. не будут пустыми. Например, в таблице b_catalog_product на столбце ID нет auto_increment.
         * Нужно передать ID > 0 или, строго говоря, ID !== null.
         */
        $primaryFieldsIndex = array_flip(
            $this->dataManager::getEntity()
                              ->getPrimaryArray()
        );
        $itemNonPrimaryFields = array_filter(
            $item->toArray(),
            static function ($value, string $fieldName) use ($primaryFieldsIndex) {
                return !(array_key_exists($fieldName, $primaryFieldsIndex) && is_null($value));
            },
            ARRAY_FILTER_USE_BOTH
        );

        return $this->dataManager::add($itemNonPrimaryFields);
    }

    /**
     * @param D7Item $item
     *
     * @throws Exception
     * @return UpdateResult
     */
    public function update(D7Item $item): UpdateResult
    {
        $itemAsArray = $item->toArray();
        $primary = [];
        foreach ($this->dataManager::getEntity()->getPrimaryArray() as $fieldName) {
            $primary[$fieldName] = $itemAsArray[$fieldName];
        }

        return $this->dataManager::update($primary, $itemAsArray);
    }

    /**
     * @param mixed $primary
     *
     * @throws Exception
     * @return DeleteResult
     */
    public function delete($primary): DeleteResult
    {
        return $this->dataManager::delete($primary);
    }
}
