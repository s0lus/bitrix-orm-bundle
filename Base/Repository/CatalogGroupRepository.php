<?php

namespace Prokl\BitrixOrmBundle\Base\Repository;

use Prokl\BitrixOrmBundle\Base\Collection\CdbResultItemCollection;
use Prokl\BitrixOrmBundle\Base\Factories\CatalogGroupFactory;
use Prokl\BitrixOrmBundle\Base\Factories\Interfaces\CdbResultItemFactoryInterface;
use Prokl\BitrixOrmBundle\Base\Query\CatalogGroupQuery;
use Prokl\BitrixOrmBundle\Base\Query\CdbResultQuery;

/**
 * Class CatalogGroupRepository
 *
 * Работа с типами цен.
 *
 * @package Prokl\BitrixOrmBundle\Base\\Repository
 */
class CatalogGroupRepository extends CdbResultRepository
{
    public function __construct(CatalogGroupFactory $factory)
    {
        parent::__construct($factory);
    }

    /**
     * @return CatalogGroupFactory
     */
    public function getFactory(): CdbResultItemFactoryInterface
    {
        return parent::getFactory();
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
        return parent::findBy($criteria, $order, $limit, $offset);
    }

    /**
     * @inheritDoc
     * @return CatalogGroupQuery
     */
    protected function createQuery(): CdbResultQuery
    {
        return new CatalogGroupQuery();
    }
}
