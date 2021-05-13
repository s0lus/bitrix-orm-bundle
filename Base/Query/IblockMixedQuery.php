<?php

namespace Prokl\BitrixOrmBundle\Base\Query;

use CDBResult;
use CIBlockResult;
use CIBlockSection;

/**
 * Class IblockMixedQuery
 *
 * @internal Сомнительно, что это когда-нибудь пригодится. Репозиторий под такой запрос сделать тяжело, т.к. в
 *     результате сразу два типа сущностей: элементы и разделы инфоблока.
 *
 * @package Prokl\BitrixOrmBundle\Base\\Query
 */
class IblockMixedQuery extends IblockSectionQuery
{
    /**
     * @return array|bool|CDBResult|CIBlockResult|int
     */
    public function exec()
    {
        return CIBlockSection::GetMixedList(
            $this->getOrder(),
            $this->getFilter(),
            $this->isCountElements(),
            $this->getSelect()
        );
    }

}
