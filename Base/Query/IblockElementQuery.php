<?php

namespace Prokl\BitrixOrmBundle\Base\Query;

use Prokl\BitrixOrmBundle\Base\Model\IblockElement;
use Bitrix\Main\Entity\AddResult;
use Bitrix\Main\Entity\DeleteResult;
use Bitrix\Main\Entity\UpdateResult;
use Bitrix\Main\Error;
use CApplicationException;
use CIBlockElement;
use CIBlockResult;

/**
 * Class IblockElementQuery
 * @package Prokl\BitrixOrmBundle\Base\Query
 */
class IblockElementQuery extends CdbResultQuery
{
    /**
     * @var CIBlockElement $CIBlockElement
     */
    private static $CIBlockElement;

    /**
     * @return CIBlockResult|integer
     */
    public function exec()
    {
        return $this->getCIBlockElement()
                    ->GetList(
                        $this->getOrder(),
                        $this->getFilter(),
                        $this->getGroup() ?: false,
                        $this->getNavParams()->toArray(),
                        $this->getSelect()
                    );
    }

    /**
     * @param IblockElement $element
     * @param boolean       $workflow
     * @param boolean       $updateSearch
     * @param boolean       $resizePictures
     *
     * @return AddResult
     */
    public function add(
        IblockElement $element,
        bool $workflow = false,
        bool $updateSearch = true,
        bool $resizePictures = false
    ): AddResult {
        $addResult = new AddResult();

        $id = $this->getCIBlockElement()
                   ->Add($element->toArray(), $workflow, $updateSearch, $resizePictures);
        if ($id > 0) {
            $element->setId($id);
            $addResult->setId($id);
        } else {
            $addResult->addError(new Error($this->getCIBlockElement()->LAST_ERROR));
        }

        return $addResult;
    }

    /**
     * @param IblockElement $element
     * @param boolean       $workflow
     * @param boolean       $updateSearch
     * @param boolean       $resizePictures
     * @param boolean       $checkDiskQuota
     *
     * @return UpdateResult
     */
    public function update(
        IblockElement $element,
        bool $workflow = false,
        bool $updateSearch = true,
        bool $resizePictures = false,
        bool $checkDiskQuota = true
    ): UpdateResult {

        $updateResult = new UpdateResult();

        $elementAsArray = $element->toArray();
        $fieldValues = $elementAsArray;
        if (
            isset($elementAsArray[IblockElement::PROPERTY_VALUES])
            && is_array($elementAsArray[IblockElement::PROPERTY_VALUES])
            && count($elementAsArray[IblockElement::PROPERTY_VALUES]) > 0
        ) {
            $propertyValues = $elementAsArray[IblockElement::PROPERTY_VALUES];
            unset($fieldValues[IblockElement::PROPERTY_VALUES]);
        }

        $update = $this->getCIBlockElement()
                       ->Update(
                           $element->getId(),
                           $fieldValues,
                           $workflow,
                           $updateSearch,
                           $resizePictures,
                           $checkDiskQuota
                       );

        if ($update) {
            $updateResult->setPrimary([$element->getId()]);
        } else {
            $updateResult->addError(new Error($this->getCIBlockElement()->LAST_ERROR));
        }

        if (
            $updateResult->isSuccess()
            && isset($propertyValues)
            && is_array($propertyValues)
            && count($propertyValues) > 0
        ) {
            CIBlockElement::SetPropertyValuesEx($element->getId(), $element->getIblockId(), $propertyValues);
        }

        return $updateResult;
    }

    /**
     * @param integer $id ID.
     *
     * @return DeleteResult
     */
    public function delete(int $id): DeleteResult
    {
        global $APPLICATION;

        $deleteResult = new DeleteResult();

        if (!$this->getCIBlockElement()::Delete($id)) {
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
     * @return CIBlockElement
     */
    protected function getCIBlockElement(): CIBlockElement
    {
        if (is_null(self::$CIBlockElement)) {
            self::$CIBlockElement = new CIBlockElement();
        }

        return self::$CIBlockElement;
    }
}
