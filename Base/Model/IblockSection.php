<?php

namespace Prokl\BitrixOrmBundle\Base\Model;

use Prokl\BitrixOrmBundle\Base\Model\Interfaces\FileRepositoryAwareInterface;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\HasActiveInterface;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\HasCodeInterface;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\HasIblockIdInterface;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\HasIblockSectionIdInterface;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\HasLeftAndRightMarginsInterface;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\HasListPageUrlInterface;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\HasNameInterface;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\HasSortInterface;
use Prokl\BitrixOrmBundle\Base\Model\Iprop\IpropSection;
use Prokl\BitrixOrmBundle\Base\Model\Traits\FileRepositoryAwareTrait;
use Prokl\BitrixOrmBundle\Base\Model\Traits\HasActiveAsStringTrait;
use Prokl\BitrixOrmBundle\Base\Model\Traits\HasChildrenTrait;
use Prokl\BitrixOrmBundle\Base\Model\Traits\HasCodeTrait;
use Prokl\BitrixOrmBundle\Base\Model\Traits\HasIblockIdTrait;
use Prokl\BitrixOrmBundle\Base\Model\Traits\HasIblockSectionIdTrait;
use Prokl\BitrixOrmBundle\Base\Model\Traits\HasLeftAndRightMarginsTrait;
use Prokl\BitrixOrmBundle\Base\Model\Traits\HasListPageUrlTrait;
use Prokl\BitrixOrmBundle\Base\Model\Traits\HasNameTrait;
use Prokl\BitrixOrmBundle\Base\Model\Traits\HasSortTrait;
use Prokl\BitrixOrmBundle\Base\Type\TextContent;
use Bitrix\Iblock\InheritedProperty\SectionValues;
use Prokl\BitrixOrmBundle\Tools\Convertors\BitrixBool;

/**
 * Class IblockSection
 * @package Prokl\BitrixOrmBundle\Base\Model
 */
abstract class IblockSection extends BitrixArrayItemBase implements
    HasActiveInterface,
    HasIblockIdInterface,
    HasIblockSectionIdInterface,
    HasCodeInterface,
    HasNameInterface,
    HasSortInterface,
    HasListPageUrlInterface,
    FileRepositoryAwareInterface,
    HasLeftAndRightMarginsInterface
{
    use HasNameTrait;
    use HasSortTrait;
    use HasActiveAsStringTrait;
    use HasIblockIdTrait;
    use HasIblockSectionIdTrait;
    use HasCodeTrait;
    use HasListPageUrlTrait;
    use HasChildrenTrait;
    use FileRepositoryAwareTrait;
    use HasLeftAndRightMarginsTrait;

     /**
     * @var boolean
     */
    protected $globalActive = true;

    /**
     * @var integer
     */
    protected $DEPTH_LEVEL;

    /**
     * @var string
     */
    protected $SECTION_PAGE_URL;

    /**
     * @var array|int
     */
    protected $PICTURE;

    /**
     * @var File
     */
    protected $picture;

    /**
     * @var IpropSection
     */
    protected $iProperty;

    /**
     * @var string
     */
    protected $DESCRIPTION;

    /**
     * @var string
     */
    protected $DESCRIPTION_TYPE;

    /**
     * @var integer
     */
    protected $ELEMENT_CNT;

    /**
     * @var TextContent
     */
    protected $description;

    public function __construct(array $fields = [], bool $useOriginal = false)
    {
        parent::__construct($fields, $useOriginal);
        $nonInitializedFields = $this->getNonInitializedFields();
        if (isset($nonInitializedFields['ACTIVE'])) {
            $this->setActive(BitrixBool::stringToBool($nonInitializedFields['ACTIVE']));
            unset($nonInitializedFields['ACTIVE']);
            $this->setNonInitializedFields($nonInitializedFields);
        } elseif (isset($nonInitializedFields['GLOBAL_ACTIVE'])) {
            $this->setGlobalActive(BitrixBool::stringToBool($nonInitializedFields['GLOBAL_ACTIVE']));
            unset($nonInitializedFields['GLOBAL_ACTIVE']);
            $this->setNonInitializedFields($nonInitializedFields);
        }
    }

    /**
     * @return integer
     */
    public function getParentId(): int
    {
        return $this->getIblockSectionId();
    }

    /**
     * @param integer $parentId
     *
     * @return $this
     */
    public function setParentId(int $parentId)
    {
        return $this->setIblockSectionId($parentId);
    }

    /**
     * @return integer
     */
    public function getDepthLevel(): int
    {
        return (int)$this->DEPTH_LEVEL;
    }

    /**
     * @param integer $level
     *
     * @return $this
     */
    public function setDepthLevel(int $level)
    {
        $this->DEPTH_LEVEL = $level;

        return $this;
    }

    /**
     * @return string
     */
    public function getSectionPageUrl(): string
    {
        return (string)$this->SECTION_PAGE_URL;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setSectionPageUrl(string $url)
    {
        $this->SECTION_PAGE_URL = $url;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isGlobalActive(): bool
    {
        return (bool)$this->globalActive;
    }

    /**
     * @param boolean $globalActive
     *
     * @return $this
     */
    public function setGlobalActive(bool $globalActive)
    {
        $this->globalActive = $globalActive;

        return $this;
    }

    /**
     * @return integer
     */
    public function getPictureId(): int
    {
        return $this->getPicture()->getId();
    }

    /**
     * @param integer $id
     *
     * @return $this
     */
    public function setPictureId(int $id)
    {
        $this->PICTURE = $id;
        $this->picture = null;

        return $this;
    }

    /**
     * @return File
     */
    public function getPicture(): File
    {
        if (is_null($this->picture)) {
            $this->picture = (self::getFileRepository())->getFileById((int)$this->PICTURE);
        }

        return $this->picture;
    }

    /**
     * @param File $picture
     *
     * @return $this
     */
    public function setPicture(File $picture)
    {
        $this->picture = $picture;
        $this->PICTURE = $picture->getValueForSaving();

        return $this;
    }

    /**
     * @return IpropSection
     */
    public function getIProperty(): IpropSection
    {
        if (is_null($this->iProperty)) {
            $this->iProperty = new IpropSection(new SectionValues($this->getIblockId(), $this->getId()));
        }

        return $this->iProperty;
    }

    /**
     * @param IpropSection $iProperty
     *
     * @return $this
     */
    public function setIProperty(IpropSection $iProperty)
    {
        $this->iProperty = $iProperty;

        return $this;
    }

    /**
     * @return TextContent
     */
    public function getDescription(): TextContent
    {
        if (is_null($this->description)) {
            $this->description = (new TextContent())->setText((string)$this->DESCRIPTION)
                                                    ->setType((string)$this->DESCRIPTION_TYPE);
        }

        return $this->description;
    }

    /**
     * @param TextContent $textContent
     *
     * @return $this
     */
    public function setDescription(TextContent $textContent)
    {
        $this->description = $textContent;
        $this->DESCRIPTION = $textContent->getText();
        $this->DESCRIPTION_TYPE = $textContent->getType();

        return $this;
    }

    /**
     * @return integer
     */
    public function getElementCount(): int
    {
        return (int)$this->ELEMENT_CNT;
    }

    /**
     * @param integer $elementCount
     *
     * @return $this
     */
    public function setElementCount(int $elementCount)
    {
        $this->ELEMENT_CNT = $elementCount;

        return $this;
    }
}
