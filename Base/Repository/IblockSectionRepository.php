<?php

namespace Prokl\BitrixOrmBundle\Base\Repository;

use Prokl\BitrixOrmBundle\Base\Collection\CdbResultItemCollection;
use Prokl\BitrixOrmBundle\Base\Enum\IblockSectionFilter;
use Prokl\BitrixOrmBundle\Base\Exception\ItemNotFoundException;
use Prokl\BitrixOrmBundle\Base\Factories\IblockSectionFactory;
use Prokl\BitrixOrmBundle\Base\Factories\Interfaces\CdbResultItemFactoryInterface;
use Prokl\BitrixOrmBundle\Base\Model\IblockSection;
use Prokl\BitrixOrmBundle\Base\Query\CdbResultQuery;
use Prokl\BitrixOrmBundle\Base\Query\IblockSectionQuery;
use Prokl\BitrixOrmBundle\Tools\Convertors\BitrixBool;
use Bitrix\Main\Entity\AddResult;
use Bitrix\Main\Entity\DeleteResult;
use Bitrix\Main\Entity\UpdateResult;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;
use function array_merge;

/**
 * Class IblockSectionRepository
 * @package Prokl\BitrixOrmBundle\Base\Repository
 */
abstract class IblockSectionRepository extends CdbResultRepository
{
    /**
     * @var integer $iblockId
     */
    private $iblockId;

    /**
     * IblockSectionRepository constructor.
     *
     * @param integer              $iblockId
     * @param IblockSectionFactory $factory
     */
    public function __construct(int $iblockId, IblockSectionFactory $factory)
    {
        parent::__construct($factory);
        $this->iblockId = $iblockId;
    }

    /**
     * @param IblockSection $section
     *
     * @return AddResult
     */
    public function add(IblockSection $section): AddResult
    {
        $section->setIblockId($this->getIblockId());
        $addResult = $this->createQuery()
                          ->add($section);

        if ($addResult->isSuccess(true)) {
            $section->setId($addResult->getId());
            $this->getFactory()->getObjectWatcher()->removeItem($section);
        }

        return $addResult;
    }

    /**
     * @return IblockSectionQuery
     */
    protected function createQuery(): CdbResultQuery
    {
        return new IblockSectionQuery();
    }

    /**
     * @return CdbResultItemFactoryInterface
     */
    public function getFactory(): CdbResultItemFactoryInterface
    {
        return parent::getFactory();
    }

    /**
     * @param IblockSection $section
     *
     * @return UpdateResult
     */
    public function update(IblockSection $section): UpdateResult
    {
        $updateResult = $this->createQuery()
                             ->update($section);

        if ($updateResult->isSuccess(true)) {
            $this->getFactory()->getObjectWatcher()->removeItem($section);
        }

        return $updateResult;
    }

    /**
     * @param IblockSection $section
     *
     * @return DeleteResult
     */
    public function delete(IblockSection $section): DeleteResult
    {
        return $this->deleteById($section->getId());
    }

    /**
     * @param integer $id
     *
     * @throws ItemNotFoundException
     * @return IblockSection
     */
    public function findById(int $id): IblockSection
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('Id must be positive integer number.');
        }

        $item = $this->getFactory()->getObjectWatcher()->get($this->getFactory()->getItemType(), $id);
        if ($item instanceof IblockSection) {
            return $item;
        }

        $collection = $this->findBy(['=ID' => $id], [], 1);

        if ($collection->count() !== 1) {
            throw new ItemNotFoundException(
                sprintf(
                    'Iblock section of type %s having ID=%d not found.',
                    $this->getFactory()->getItemType(),
                    $id
                )
            );
        }

        return $collection->current();
    }

    /**
     * @param array $criteria
     * @param array $order
     * @param integer $limit
     * @param integer $offset
     *
     * @return CdbResultItemCollection
     */
    public function findBy(
        array $criteria,
        array $order = ['SORT' => 'ASC', 'NAME' => 'ASC'],
        int $limit = 0,
        int $offset = 0
    ): CdbResultItemCollection {
        $result = $this->createQuery()
                       ->setSelect($this->getFactory()->getSelect())
                       ->setFilter(array_merge($criteria, ['IBLOCK_ID' => $this->getIBlockId()]))
                       ->setOrder($order)
                       ->setLimit($limit)
                       ->setOffset($offset)
                       ->setCountElements($this->hasElementCountCriteria($criteria))
                       ->exec();

        return $this->getFactory()->createCollection($result);
    }

    /**
     * Возвращает id инфоблока, с которым ведётся работа в данном репозитории.
     *
     * @return integer
     */
    public function getIblockId(): int
    {
        return $this->iblockId;
    }

    /**
     * @param integer $id
     *
     * @throws ItemNotFoundException
     * @return IblockSection
     */
    public function findActiveById(int $id): IblockSection
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('Id must be positive integer number.');
        }

        $collection = $this->findActiveBy(['=ID' => $id], [], 1);

        if ($collection->count() !== 1) {
            throw new ItemNotFoundException(
                sprintf(
                    'Active iblock section of type %s having ID=%d not found.',
                    $this->getFactory()->getItemType(),
                    $id
                )
            );
        }

        return $collection->current();
    }

    public function findActiveBy(
        array $criteria,
        array $order = ['SORT' => 'ASC', 'NAME' => 'ASC'],
        int $limit = 0,
        int $offset = 0
    ): CdbResultItemCollection {

        return $this->findBy(
            array_merge($criteria, static::getActiveAccessibleSectionsFilter()),
            $order,
            $limit,
            $offset
        );
    }

    /**
     * @param boolean $checkPermissions
     *
     * @return array
     * @since 3.7.0
     */
    public static function getActiveAccessibleSectionsFilter(bool $checkPermissions = true): array
    {
        return [
            'ACTIVE'            => BitrixBool::TRUE,
            'CHECK_PERMISSIONS' => BitrixBool::boolToString($checkPermissions),
        ];
    }

    /**
     * @return array
     * @deprecated Из-за грамматической ошибки "Accessable".
     */
    public static function getActiveAccessableSectionsFilter(): array
    {
        return static::getActiveAccessibleSectionsFilter();
    }

    /**
     * @param string $code
     *
     * @throws ItemNotFoundException
     * @return IblockSection
     */
    public function findActiveByCode(string $code): IblockSection
    {
        $code = trim($code);
        if ('' === $code) {
            throw new InvalidArgumentException('Code must be specified.');
        }

        $collection = $this->findActiveBy(['=CODE' => $code], [], 1);

        if ($collection->count() !== 1) {
            throw new ItemNotFoundException(
                sprintf(
                    'Active iblock section of type %s having CODE=`%s` not found.',
                    $this->getFactory()->getItemType(),
                    $code
                )
            );
        }

        return $collection->current();
    }

    /**
     * @param string $code
     *
     * @throws ItemNotFoundException
     * @return IblockSection
     */
    public function findByCode(string $code): IblockSection
    {
        $code = trim($code);
        if ('' === $code) {
            throw new InvalidArgumentException('Code must be specified.');
        }

        $collection = $this->findBy(['=CODE' => $code], [], 1);

        if ($collection->count() !== 1) {
            throw new ItemNotFoundException(
                sprintf(
                    'Iblock section of type %s having CODE=`%s` not found.',
                    $this->getFactory()->getItemType(),
                    $code
                )
            );
        }

        return $collection->current();
    }

    /**
     * Получить все разделы в виде дерева
     *
     * @param array $criteria
     * @param array $order Предположительно, параметр ошибочный и может повредить дерево или вообще не работать, т.к.
     *     априори не может быть двух разделов с одинаковым LEFT_MARGIN .
     *
     * @return CdbResultItemCollection
     */
    public function getTree(array $criteria, array $order = ['SORT' => 'ASC', 'NAME' => 'ASC'])
    {
        $resultCollection = new CdbResultItemCollection();
        $rawCollection = $this->findBy(
            $criteria,
            array_merge(['LEFT_MARGIN' => 'ASC'], $order)
        );

        $createTree = function (ArrayCollection $collection, int $depth) use ($rawCollection, &$createTree) : bool {
            $rawCollection->forAll(
                function (
                    /** @noinspection PhpUnusedParameterInspection */
                    int $id,
                    IblockSection $section
                ) use ($collection, $depth, $createTree, $rawCollection) {
                    if ($section->getDepthLevel() > $depth) {
                        $createTree($collection->last()->getChildren(), $section->getDepthLevel());
                    } elseif ($section->getDepthLevel() === $depth) {
                        $collection->add($section);
                        $rawCollection->removeElement($section);
                    } else {
                        return false;
                    }

                    return true;
                }
            );
        };

        $createTree($resultCollection, 1);

        return $resultCollection;
    }

    /**
     * Проверяет, содержит ли фильтр хотя бы одно условие, влекущее необходимость подсчёта количества элементов в
     * разделе.
     *
     * @param array $criteria
     *
     * @return bool
     * @see https://dev.1c-bitrix.ru/api_help/iblock/classes/ciblocksection/getlist.php
     */
    private function hasElementCountCriteria(array $criteria): bool
    {
        $elementCountKeys = [
            IblockSectionFilter::ELEMENT_SUBSECTIONS,
            IblockSectionFilter::CNT_ALL,
            IblockSectionFilter::CNT_ACTIVE,
        ];
        foreach ($elementCountKeys as $key) {
            if (
                array_key_exists($key, $criteria)
                && (
                    $criteria[$key] === BitrixBool::FALSE
                    || $criteria[$key] === BitrixBool::TRUE
                )
            ) {
                return true;
            }
        }

        return false;
    }
}
