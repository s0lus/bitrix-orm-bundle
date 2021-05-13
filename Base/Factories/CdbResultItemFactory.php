<?php

namespace Prokl\BitrixOrmBundle\Base\Factories;

use CDBResult;
use Generator;
use InvalidArgumentException;
use Prokl\BitrixOrmBundle\Base\Collection\CdbResultItemCollection;
use Prokl\BitrixOrmBundle\Base\Factories\Interfaces\CdbResultItemFactoryInterface;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\BitrixArrayItemInterface;

/**
 * Class CdbResultItemFactory
 * @package Prokl\BitrixOrmBundle\Base\Factories
 */
abstract class CdbResultItemFactory extends ItemFactoryBase implements CdbResultItemFactoryInterface
{

    /**
     * @param CDBResult $result
     *
     * @return CdbResultItemCollection
     */
    public function createCollection(CDBResult $result): CdbResultItemCollection
    {
        $collection = (new CdbResultItemCollection())->setCdbResult($result);

        return $this->populateCollection($result, $collection);
    }

    /**
     * Возвращает коллекцию, созданную из массива объектов. Метод нужен, когда коллекция создаётся из массива объектов
     * и должна иметь возможность вывода постраничной навигации для отражения существования бОльшего количества
     * элементов.
     *
     * @param array $objects
     * @param int|null $offset
     * @param int|null $limit
     * @param int|null $total
     *
     * @return CdbResultItemCollection
     */
    public function createCollectionFromArray(
        array $objects,
        int $offset = null,
        int $limit = null,
        int $total = null
    ): CdbResultItemCollection {

        $result = new CDBResult();
        $result->InitFromArray($objects);

        $collection = (new CdbResultItemCollection())->setCdbResult($result);
        $collection = $this->populateCollection($result, $collection);

        if (isset($offset, $limit, $total)) {
            if ($offset < 0) {
                throw new InvalidArgumentException('Offset must be non-negative number.');
            }
            if ($limit <= 0) {
                throw new InvalidArgumentException('Limit must be positive number.');
            }
            if ($total < 0) {
                throw new InvalidArgumentException('Total must be non-negative number.');
            }

            $collection->getCdbResult()->nSelectedCount = $total;
            $collection->getCdbResult()->NavRecordCount = $total;
            $collection->getCdbResult()->NavPageSize = $limit;
            $collection->getCdbResult()->NavPageCount = (int)ceil($total / $limit);
            $collection->getCdbResult()->NavPageNomer = (int)floor($offset / $limit) + 1;
        }

        return $collection;
    }

    /**
     * Извлекает данные из ресурса БД.
     *
     * @inheritdoc Метод должен обязательно производить проверку: если массив, то создавать объект или же просто
     *     проверять тип объекта и возвращать его.
     *
     * @return Generator
     */
    protected function fetchItem(CDBResult $result): Generator
    {
        /**
         * \CAllDBResult::GetNext() неприменим, если CDBResult инициирован из массива объектов:
         * он возвращает пустой массив, когда сталкивается с объектом.
         */
        if ($result->bFromArray) {
            while ($fields = $result->Fetch()) {
                yield $this->createItemSafely($fields);
            }
        } else {
            while ($fields = $result->GetNext()) {
                yield $this->createItemSafely($fields);
            }
        }
    }

    /**
     * @param CDBResult $result
     * @param $collection
     *
     * @return mixed
     */
    protected function populateCollection(CDBResult $result, CdbResultItemCollection $collection)
    {
        foreach ($this->fetchItem($result) as $item) {
            if ($item instanceof BitrixArrayItemInterface) {
                $collection->set($item->getId(), $item);
            }
        }

        return $collection;
    }
}
