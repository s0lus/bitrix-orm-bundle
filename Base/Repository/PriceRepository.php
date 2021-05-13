<?php

namespace Prokl\BitrixOrmBundle\Base\Repository;

use Prokl\BitrixOrmBundle\Base\Factories\D7ItemFactory;
use Prokl\BitrixOrmBundle\Base\Factories\PriceFactory;
use Prokl\BitrixOrmBundle\Base\ObjectWatcher;
use Bitrix\Catalog\PriceTable;
use Bitrix\Main\Entity\DataManager;

/**
 * Class PriceRepository
 * @package Prokl\BitrixOrmBundle\Base\\Repository
 */
class PriceRepository extends D7Repository
{
    public function __construct(DataManager $dataManager = null, D7ItemFactory $factory = null)
    {
        if (is_null($dataManager)) {
            $dataManager = new PriceTable();
        }
        if (is_null($factory)) {
            $factory = new PriceFactory(new ObjectWatcher());
        }
        parent::__construct($dataManager, $factory);
    }
}
