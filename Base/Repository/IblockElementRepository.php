<?php

namespace Prokl\BitrixOrmBundle\Base\Repository;

use Prokl\BitrixOrmBundle\Base\Collection\CdbResultItemCollection;
use Prokl\BitrixOrmBundle\Base\Exception\ItemNotFoundException;
use Prokl\BitrixOrmBundle\Base\Factories\IblockElementFactory;
use Prokl\BitrixOrmBundle\Base\Factories\Interfaces\CdbResultItemFactoryInterface;
use Prokl\BitrixOrmBundle\Base\Model\IblockElement;
use Prokl\BitrixOrmBundle\Base\Query\CdbResultQuery;
use Prokl\BitrixOrmBundle\Base\Query\IblockElementQuery;
use Bitrix\Main\Entity\AddResult;
use Bitrix\Main\Entity\DeleteResult;
use Bitrix\Main\Entity\UpdateResult;
use InvalidArgumentException;
use Prokl\BitrixOrmBundle\Tools\Convertors\BitrixBool;

/**
 * Class IblockElementRepository
 * @package Prokl\BitrixOrmBundle\Base\Repository
 */
abstract class IblockElementRepository extends CdbResultRepository
{
    /**
     * @var integer
     */
    private $iblockId;

    /**
     * IblockElementRepository constructor.
     *
     * @param integer              $iblockId ID инфоблока.
     * @param IblockElementFactory $factory  Фабрика.
     */
    public function __construct(int $iblockId, IblockElementFactory $factory)
    {
        parent::__construct($factory);
        $this->iblockId = $iblockId;
    }

    /**
     * @return IblockElementFactory
     */
    public function getFactory(): CdbResultItemFactoryInterface
    {
        return parent::getFactory();
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
     * @param IblockElement $element Элемент.
     *
     * @return AddResult
     */
    public function add(IblockElement $element): AddResult
    {
        $element->setIblockId($this->getIblockId());
        $addResult = $this->createQuery()
                          ->add($element);

        if ($addResult->isSuccess(true)) {
            $element->setId($addResult->getId());
            $this->getFactory()->getObjectWatcher()->removeItem($element);
        }

        return $addResult;
    }

    /**
     * @param IblockElement $element Элемент.
     *
     * @return UpdateResult
     */
    public function update(IblockElement $element): UpdateResult
    {
        $updateResult = $this->createQuery()
                             ->update($element);

        if ($updateResult->isSuccess(true)) {
            $this->getFactory()->getObjectWatcher()->removeItem($element);
        }

        return $updateResult;
    }

    /**
     * @param IblockElement $element Элемент.
     *
     * @return DeleteResult
     */
    public function delete(IblockElement $element): DeleteResult
    {
        return $this->deleteById($element->getId());
    }

    /**
     * @inheritDoc
     */
    public function findBy(
        array $criteria,
        array $order = ['SORT' => 'ASC', 'NAME' => 'ASC'],
        int $limit = 0,
        int $offset = 0
    ): CdbResultItemCollection {
        return parent::findBy(
            array_merge($criteria, ['IBLOCK_ID' => $this->getIBlockId()]),
            $order,
            $limit,
            $offset
        );
    }

    /**
     * @param array   $criteria
     * @param array   $order
     * @param integer $limit
     * @param integer $offset
     *
     * @return CdbResultItemCollection
     */
    public function findActiveBy(
        array $criteria,
        array $order = ['SORT' => 'ASC', 'NAME' => 'ASC'],
        int $limit = 0,
        int $offset = 0
    ): CdbResultItemCollection {

        return $this->findBy(
            array_merge($criteria, static::getActiveAccessibleElementsFilter()),
            $order,
            $limit,
            $offset
        );
    }

    /**
     * @param integer $id
     *
     * @throws ItemNotFoundException
     * @return IblockElement
     */
    public function findActiveById(int $id): IblockElement
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('Id must be positive integer number.');
        }

        $collection = $this->findActiveBy(['=ID' => $id], [], 1);

        if ($collection->count() !== 1) {
            throw new ItemNotFoundException(
                sprintf(
                    'Active iblock element of type %s having ID=%d not found.',
                    $this->getFactory()->getItemType(),
                    $id
                )
            );
        }

        return $collection->current();
    }

    /**
     * @param string $code
     *
     * @throws ItemNotFoundException
     * @return IblockElement
     */
    public function findActiveByCode(string $code): IblockElement
    {
        $code = trim($code);
        if ('' === $code) {
            throw new InvalidArgumentException('Code must be specified.');
        }

        $collection = $this->findActiveBy(['=CODE' => $code], [], 1);

        if ($collection->count() !== 1) {
            throw new ItemNotFoundException(
                sprintf(
                    'Active iblock element of type %s having CODE=`%s` not found.',
                    $this->getFactory()->getItemType(),
                    $code
                )
            );
        }

        return $collection->current();
    }

    /**
     * @param integer $id
     *
     * @throws ItemNotFoundException
     * @return IblockElement
     */
    public function findById(int $id): IblockElement
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('Id must be positive integer number.');
        }

        $item = $this->getFactory()->getObjectWatcher()->get($this->getFactory()->getItemType(), $id);
        if ($item instanceof IblockElement) {
            return $item;
        }

        $collection = $this->findBy(['=ID' => $id], [], 1);

        if ($collection->count() !== 1) {
            throw new ItemNotFoundException(
                sprintf(
                    'Iblock element of type %s having ID=%d not found.',
                    $this->getFactory()->getItemType(),
                    $id
                )
            );
        }

        return $collection->current();
    }

    /**
     * @param string $code
     *
     * @throws ItemNotFoundException
     * @return IblockElement
     */
    public function findByCode(string $code): IblockElement
    {
        $code = trim($code);
        if ('' === $code) {
            throw new InvalidArgumentException('Code must be specified.');
        }

        $collection = $this->findBy(['=CODE' => $code], [], 1);

        if ($collection->count() !== 1) {
            throw new ItemNotFoundException(
                sprintf(
                    'Iblock element of type %s having CODE=`%s` not found.',
                    $this->getFactory()->getItemType(),
                    $code
                )
            );
        }

        return $collection->current();
    }

    /**
     * @return IblockElementQuery
     */
    protected function createQuery(): CdbResultQuery
    {
        return new IblockElementQuery();
    }

    /**
     * @return array
     * @deprecated Из-за грамматической ошибки "Accessable".
     */
    public static function getActiveAccessableElementsFilter(): array
    {
        return static::getActiveAccessibleElementsFilter();
    }

    /**
     * Возвращает фильтр активных и доступных элементов инфоблока.
     *
     * Это базовая основа и в публичной части всегда рекомендуется использовать такой фильтр, чтобы можно было всегда
     * управлять доступами, а также флажком и датами активности.
     *
     * @param boolean $checkPermissions
     *
     * @return array
     */
    public static function getActiveAccessibleElementsFilter(bool $checkPermissions = true): array
    {
        return [
            'CHECK_PERMISSIONS' => BitrixBool::boolToString($checkPermissions),
            'ACTIVE'            => BitrixBool::TRUE,
            'ACTIVE_DATE'       => BitrixBool::TRUE,
        ];
    }

}
