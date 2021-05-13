<?php

namespace Prokl\BitrixOrmBundle\Base\Repository;

use Prokl\BitrixOrmBundle\Base\Collection\CdbResultItemCollection;
use Prokl\BitrixOrmBundle\Base\Exception\ItemNotFoundException;
use Prokl\BitrixOrmBundle\Base\Factories\Interfaces\CdbResultItemFactoryInterface;
use Prokl\BitrixOrmBundle\Base\Query\CdbResultQuery;
use Bitrix\Main\Entity\DeleteResult;
use Bitrix\Main\Error;

/**
 * Class CdbResultRepository
 * @package Prokl\BitrixOrmBundle\Base\Repository
 */
abstract class CdbResultRepository
{
    /**
     * @var CdbResultItemFactoryInterface
     */
    private $factory;

    /**
     * CdbResultRepository constructor.
     *
     * @param CdbResultItemFactoryInterface $factory Фабрика.
     */
    public function __construct(CdbResultItemFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @return CdbResultItemFactoryInterface
     */
    public function getFactory(): CdbResultItemFactoryInterface
    {
        return $this->factory;
    }

    /**
     * @return CdbResultQuery
     */
    abstract protected function createQuery(): CdbResultQuery;

    /**
     * Возвращает сущности в соответствии с заданными условиями.
     *
     * @param array   $criteria Условия фильтрации.
     * @param array   $order    Условия сортировки.
     * @param integer $limit    Лимит количества сущностей.
     * @param integer $offset   Смещение по списку сущностей.
     *
     * @return CdbResultItemCollection
     */
    public function findBy(array $criteria, array $order = [], int $limit = 0, int $offset = 0): CdbResultItemCollection
    {
        //TODO Экономия запроса к БД, если фильтр только по ID или списку ID
        $result = $this->createQuery()
                       ->setSelect($this->getFactory()->getSelect())
                       ->setFilter($criteria)
                       ->setOrder($order)
                       ->setLimit($limit)
                       ->setOffset($offset)
                       ->exec();

        return $this->getFactory()->createCollection($result);
    }

    /**
     * Возвращает сущности по списку идентификаторов и в соответствии с заданными условиями.
     *
     * @param int[]|string[] $idList   Список идентификаторов.
     * @param array          $criteria Дополнительные условия фильтрации.
     * @param array          $order    Условия сортировки.
     * @param integer        $limit    Лимит количества сущностей.
     * @param integer        $offset   Смещение по списку сущностей.
     *
     * @return CdbResultItemCollection
     */
    public function findByIdList(
        array $idList,
        array $criteria = [],
        array $order = [],
        int $limit = 0,
        int $offset = 0
    ): CdbResultItemCollection {

        if (count($idList) === 0) {
            return $this->getFactory()->createCollectionFromArray([]);
        }

        return $this->findBy(array_merge($criteria, ['=ID' => $idList]), $order, $limit, $offset);
    }

    /**
     * Удаляет сущность по идентификатору.
     *
     * @param integer $id         ID.
     * @param boolean $safeDelete Безопасное удаление: не позволяет удалить сущность, если над ней не ведётся управление
     *     настоящим репозиторием.
     *
     * @return DeleteResult
     */
    public function deleteById(int $id, bool $safeDelete = true): DeleteResult
    {
        if (true === $safeDelete) {
            try {
                $this->findById($id);
            } catch (ItemNotFoundException $exception) {
                $deleteResult = new DeleteResult();
                $deleteResult->addError(new Error($exception->getMessage(), $exception->getCode()));

                return $deleteResult;
            }
        }

        $deleteResult = $this->createQuery()
                             ->delete($id);

        if ($deleteResult->isSuccess(true)) {
            $this->getFactory()
                 ->getObjectWatcher()
                 ->remove($this->getFactory()->getItemType(), $id);
        }

        return $deleteResult;
    }
}
