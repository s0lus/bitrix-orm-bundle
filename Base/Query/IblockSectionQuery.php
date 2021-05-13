<?php

namespace Prokl\BitrixOrmBundle\Base\Query;

use Prokl\BitrixOrmBundle\Base\Model\IblockSection;
use Bitrix\Main\Entity\AddResult;
use Bitrix\Main\Entity\DeleteResult;
use Bitrix\Main\Entity\UpdateResult;
use Bitrix\Main\Error;
use CApplicationException;
use CDBResult;
use CIBlockResult;
use CIBlockSection;

/**
 * Class IblockSectionQuery
 * @package Prokl\BitrixOrmBundle\Base\Query
 */
class IblockSectionQuery extends CdbResultQuery
{
    /**
     * @var CIBlockSection
     */
    private static $CIBlockSection;

     /**
     * @var boolean
     */
    protected $countElements = false;

    /**
     * @return boolean
     */
    public function isCountElements(): bool
    {
        return $this->countElements;
    }

    /**
     * @param boolean $countElements
     *
     * @return $this
     */
    public function setCountElements(bool $countElements)
    {
        $this->countElements = $countElements;

        return $this;
    }

    /**
     * @return array|boolean|CDBResult|CIBlockResult|int
     */
    public function exec()
    {
        return CIBlockSection::GetList(
            $this->getOrder(),
            $this->getFilter(),
            $this->isCountElements(),
            $this->getSelect(),
            $this->getNavParams()->toArray()
        );
    }

    /**
     * @param IblockSection $section
     * @param boolean $resort
     * @param boolean $updateSearch
     * @param boolean $resizePictures
     *
     * @return AddResult
     */
    public function add(
        IblockSection $section,
        bool $resort = true,
        bool $updateSearch = true,
        bool $resizePictures = false
    ): AddResult {
        $addResult = new AddResult();

        $id = $this->getCIBlockSection()
                   ->Add($section->toArray(), $resort, $updateSearch, $resizePictures);
        if ($id > 0) {
            $section->setId($id);
            $addResult->setId($id);
        } else {
            $addResult->addError(new Error($this->getCIBlockSection()->LAST_ERROR));
        }

        return $addResult;
    }

    /**
     * @param IblockSection $section
     * @param boolean $resort
     * @param boolean $updateSearch
     * @param boolean $resizePictures
     *
     * @return UpdateResult
     */
    public function update(
        IblockSection $section,
        bool $resort = true,
        bool $updateSearch = true,
        bool $resizePictures = false
    ): UpdateResult {
        $updateResult = new UpdateResult();

        $update = $this->getCIBlockSection()
                       ->Update($section->getId(), $section->toArray(), $resort, $updateSearch, $resizePictures);

        if ($update) {
            $updateResult->setPrimary([$section->getId()]);
        } else {
            $updateResult->addError(new Error($this->getCIBlockSection()->LAST_ERROR));
        }

        return $updateResult;
    }

    /**
     * @param integer $id
     * @param boolean $checkPermissions
     *
     * @return DeleteResult
     */
    public function delete(int $id, bool $checkPermissions = true): DeleteResult
    {
        global $APPLICATION;

        $deleteResult = new DeleteResult();

        if (!$this->getCIBlockSection()::Delete($id, $checkPermissions)) {

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
     * @return CIBlockSection
     */
    protected function getCIBlockSection(): CIBlockSection
    {
        if (is_null(self::$CIBlockSection)) {
            self::$CIBlockSection = new CIBlockSection();
        }

        return self::$CIBlockSection;
    }
}
