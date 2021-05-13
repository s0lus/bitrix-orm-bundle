<?php

namespace Prokl\BitrixOrmBundle\Base\Query;

use Bitrix\Main\Entity\DeleteResult;
use Bitrix\Main\Error;
use CApplicationException;
use CCatalogGroup;
use CDBResult;

class CatalogGroupQuery extends CdbResultQuery
{
    /**
     * @var CCatalogGroup
     */
    protected $CCatalogGroup;

    /**
     * @return bool|CDBResult|int
     */
    public function exec()
    {
        return CCatalogGroup::GetList(
            $this->getOrder(),
            $this->getFilter(),
            $this->getGroup() ?: false,
            $this->getNavParams()->toArray(),
            $this->getSelect()
        );
    }

    /**
     * @inheritDoc
     */
    public function delete(int $id): DeleteResult
    {
        global $APPLICATION;

        $deleteResult = new DeleteResult();

        if (false === $this->getCCatalogGroup()->Delete($id)) {
            $errorCode = 0;
            $errorMessage = 'unknown error';
            $applicationException = $APPLICATION->GetException();

            if ($applicationException instanceof CApplicationException) {
                $errorMessage = $applicationException->GetString();
                $errorCode = $applicationException->GetID();
            }
            $deleteResult->addError(new Error($errorMessage, $errorCode));
        }

        return $deleteResult;
    }

    /**
     * @return CCatalogGroup
     */
    protected function getCCatalogGroup(): CCatalogGroup
    {
        if (is_null($this->CCatalogGroup)) {
            $this->CCatalogGroup = new CCatalogGroup();
        }

        return $this->CCatalogGroup;
    }
}
