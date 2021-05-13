<?php

namespace Prokl\BitrixOrmBundle\Base\Repository;

use Prokl\BitrixOrmBundle\Base\Collection\D7ItemCollection;
use Prokl\BitrixOrmBundle\Base\Factories\D7ItemFactory;
use Prokl\BitrixOrmBundle\Base\Factories\HlbReferenceFactory;
use Prokl\BitrixOrmBundle\Base\Model\HlbReferenceItem;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\DB\ArrayResult;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;

class HlbReferenceRepository extends D7Repository
{
    public function __construct(DataManager $dataManager, HlbReferenceFactory $factory)
    {
        parent::__construct($dataManager, $factory);
    }

    /**
     * @return HlbReferenceFactory
     */
    public function getFactory(): D7ItemFactory
    {
        return parent::getFactory();
    }

    /**
     * @param string $xmlId
     *
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @return HlbReferenceItem
     */
    public function getReference(string $xmlId): HlbReferenceItem
    {
        if (trim($xmlId) === '') {
            return $this->getFactory()->createItem([]);
        }

        $result = $this->createQuery()->setSelect($this->getFactory()->getSelect())
                       ->setFilter(['=UF_XML_ID' => $xmlId])
                       ->setLimit(1)
                       ->exec();

        $fields = $result->fetch();

        if (false === $fields || !is_array($fields)) {

            return $this->getFactory()->createItem([]);
        }

        return $this->getFactory()->createItem($fields);
    }

    /**
     * @param array $xmlIdList
     *
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @return D7ItemCollection
     */
    public function getReferenceMulti(array $xmlIdList): D7ItemCollection
    {
        $xmlIdList = array_filter(
            $xmlIdList,
            static function ($xmlId) {
                return trim($xmlId) !== '';
            }
        );

        if (count($xmlIdList) === 0) {
            return $this->getFactory()->createCollection(new ArrayResult([]));
        }

        $result = $this->createQuery()->setSelect($this->getFactory()->getSelect())
                       ->setFilter(['=UF_XML_ID' => $xmlIdList])
                       ->exec();

        return $this->getFactory()->createCollection($result);
    }
}
