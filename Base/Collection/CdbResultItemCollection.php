<?php

namespace Prokl\BitrixOrmBundle\Base\Collection;

use CDBResult;

/**
 * Class CdbResultItemCollection
 *
 * @package Prokl\BitrixOrmBundle\Base\\Collection
 */
class CdbResultItemCollection extends CollectionBase
{
    /**
     * @var CDBResult Ссылка на объект результата запроса, т.к. только из него можно построить постраничную навигацию
     *     стандартными средствами.
     */
    protected $cdbResult;

    /**
     * CdbResultItemCollection constructor.
     *
     * @param array $elements
     */
    public function __construct(array $elements = [])
    {
        parent::__construct($elements);
        $result = new CDBResult();
        $result->InitFromArray($elements);
        $this->setCdbResult($result);
    }

    /**
     * @param CDBResult $result
     *
     * @return $this
     */
    public function setCdbResult(CDBResult $result)
    {
        $this->cdbResult = $result;

        return $this;
    }

    /**
     * @return CDBResult
     */
    public function getCdbResult(): CDBResult
    {
        return $this->cdbResult;
    }
}
