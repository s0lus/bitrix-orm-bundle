<?php

namespace Prokl\BitrixOrmBundle\Base\Repository;

use Prokl\BitrixOrmBundle\Base\Collection\D7ItemCollection;
use Prokl\BitrixOrmBundle\Base\Exception\ItemNotFoundException;
use Prokl\BitrixOrmBundle\Base\Factories\D7ItemFactory;
use Prokl\BitrixOrmBundle\Base\Model\D7Item;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\BitrixArrayItemInterface;
use Prokl\BitrixOrmBundle\Base\Query\D7Query;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\DB\ArrayResult;
use Bitrix\Main\Entity\AddResult;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\DeleteResult;
use Bitrix\Main\Entity\Field;
use Bitrix\Main\Entity\UpdateResult;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Exception;
use InvalidArgumentException;

/**
 * Class D7Repository
 * @package Prokl\BitrixOrmBundle\Base\Repository
 */
abstract class D7Repository
{
    /**
     * @var DataManager
     */
    protected $dataManager;

    /**
     * @var D7ItemFactory
     */
    protected $factory;

    /**
     * D7Repository constructor.
     *
     * @param DataManager   $dataManager
     * @param D7ItemFactory $factory
     */
    public function __construct(DataManager $dataManager, D7ItemFactory $factory)
    {
        $this->dataManager = $dataManager;
        $this->factory = $factory;
    }

    /**
     * @return D7ItemFactory
     */
    public function getFactory(): D7ItemFactory
    {
        return $this->factory;
    }

    /**
     * @return DataManager
     */
    public function getDataManager(): DataManager
    {
        return $this->dataManager;
    }

    /**
     * @param D7Item $item
     *
     * @throws Exception
     * @return AddResult
     */
    public function add(D7Item $item): AddResult
    {
        $addResult = $this->createQuery()
                          ->add($item);

        if ($addResult->isSuccess(true)) {
            $item->setId($addResult->getId());
            $this->getFactory()->getObjectWatcher()->removeItem($item);
        }

        return $addResult;
    }

    /**
     * @param D7Item $item
     *
     * @throws Exception
     * @return UpdateResult
     */
    public function update(D7Item $item): UpdateResult
    {
        $updateResult = $this->createQuery()
                             ->update($item);

        if ($updateResult->isSuccess(true)) {
            $this->getFactory()->getObjectWatcher()->removeItem($item);
        }

        return $updateResult;
    }

    /**
     * @param D7Item $item
     *
     * @throws Exception
     * @return DeleteResult
     */
    public function delete(D7Item $item): DeleteResult
    {
        return $this->deleteById($item->getId());
    }

    /**
     * @param integer $id
     *
     * @throws Exception
     * @return DeleteResult
     */
    public function deleteById(int $id): DeleteResult
    {
        $deleteResult = $this->createQuery()
                             ->delete($id);

        if ($deleteResult->isSuccess(true)) {
            $this->getFactory()->getObjectWatcher()->remove($this->getFactory()->getItemType(), $id);
        }

        return $deleteResult;
    }

    /**
     * Возвращает сущности в соответствии с заданными условиями $criteria.
     *
     * @param array   $criteria
     * @param array   $order
     * @param integer $limit
     * @param integer $offset
     * @param Field[] $runtimeFields Динамические поля. Например, чтобы ссылаться на них в $criteria.
     *
     * @throws ArgumentException | ObjectPropertyException | SystemException
     * @return D7ItemCollection
     */
    public function findBy(
        array $criteria,
        array $order = [],
        int $limit = 0,
        int $offset = 0,
        array $runtimeFields = []
    ): D7ItemCollection {
        //TODO Экономия запроса к БД, если фильтр только по ID или списку ID
        /**
         * Метод createQuery() может быть переопределён так, что на постоянной основе добавляет runtime-поля.
         * Поэтому важно добавить поля, не повредив уже существующие. При этом возможность замены всё равно
         * сохраняется, если имя нового поля совпадёт с существующим.
         */
        $d7Query = $this->createQuery();
        foreach ($runtimeFields as $runtimeField) {
            $d7Query->registerRuntimeField($runtimeField);
        }
        $result = $d7Query->setSelect($this->getFactory()->getSelect())
                          ->setFilter($criteria)
                          ->setOrder($order)
                          ->setLimit($limit)
                          ->setOffset($offset)
                          ->exec();

        return $this->getFactory()->createCollection($result);
    }

    /**
     * @param integer $id
     *
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @return D7Item
     */
    public function findById(int $id): D7Item
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('Id must be positive integer number.');
        }

        $item = $this->getFactory()->getObjectWatcher()->get($this->getFactory()->getItemType(), $id);
        if ($item instanceof BitrixArrayItemInterface) {
            /** @var D7Item $item */
            return $item;
        }

        $collection = $this->findBy(['ID' => $id], [], 1);

        if ($collection->count() !== 1) {
            throw new ItemNotFoundException(
                sprintf(
                    'Item of type %s having ID=%d not found.',
                    $this->getFactory()->getItemType(),
                    $id
                )
            );
        }

        return $collection->current();
    }

    /**
     * Возвращает сущности по списку идентификаторов и в соответствии с заданными условиями.
     *
     * @param int[]   $idList        Список идентификаторов.
     * @param array   $criteria      Дополнительные условия фильтрации.
     * @param array   $order         Условия сортировки.
     * @param integer $limit         Лимит количества сущностей.
     * @param integer $offset        Смещение по списку сущностей.
     * @param Field[] $runtimeFields Динамические поля. Например, чтобы ссылаться на них в $criteria.
     *
     * @throws ArgumentException | ObjectPropertyException | SystemException
     *
     * @return D7ItemCollection
     */
    public function findByIdList(
        array $idList,
        array $criteria = [],
        array $order = [],
        int $limit = 0,
        int $offset = 0,
        array $runtimeFields = []
    ): D7ItemCollection {
        if (count($idList) === 0) {
            return $this->getFactory()->createCollection(new ArrayResult([]));
        }

        return $this->findBy(array_merge($criteria, ['=ID' => $idList]), $order, $limit, $offset, $runtimeFields);
    }

    /**
     * @return D7Query
     */
    protected function createQuery(): D7Query
    {
        return (new D7Query($this->dataManager));
    }

}
