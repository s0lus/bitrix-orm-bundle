<?php

namespace Prokl\BitrixOrmBundle\Base\Query;

use Bitrix\Main\Entity\DeleteResult;
use CDBResult;
use CFile;

class FileQuery extends CdbResultQuery
{
    /**
     * @var array
     */
    protected $idOrder = [];

    /**
     * @param array $idList
     *
     * @return $this
     */
    public function setFilterByIdList(array $idList)
    {
        $this->setFilter(['@ID' => implode(',', $idList)]);
        if (count($idList) > 1) {
            $this->setIdOrder($idList);
        }

        return $this;
    }

    /**
     * @param array $idOrder
     *
     * @return $this
     */
    public function setIdOrder(array $idOrder)
    {
        $this->idOrder = $idOrder;

        return $this;
    }

    /**
     * @return CDBResult
     */
    public function exec(): CDBResult
    {
        $result = CFile::GetList($this->getOrder(), $this->getFilter());

        if (!$this->idOrder) {
            return $result;
        }

        $modifiedResult = array_flip($this->idOrder);
        while ($fields = $result->GetNext()) {
            $modifiedResult[$fields['ID']] = $fields;
        }

        $newResult = new CDBResult();
        $newResult->InitFromArray($modifiedResult);

        return $newResult;

    }

    /**
     * @inheritDoc
     */
    public function delete(int $id): DeleteResult
    {
        CFile::Delete($id);

        return new DeleteResult();
    }

}
