<?php

namespace Prokl\BitrixOrmBundle\Base\Model\Iprop;

use Bitrix\Iblock\InheritedProperty\BaseValues;
use Bitrix\Iblock\InheritedProperty\ElementValues;
use Prokl\BitrixOrmBundle\Base\Model\BitrixArrayItemBase;

class IpropElement extends BitrixArrayItemBase implements IpropInterface
{
    /**
     * @var string
     */
    protected $ELEMENT_META_TITLE;

    /**
     * @var string
     */
    protected $ELEMENT_META_KEYWORDS;

    /**
     * @var string
     */
    protected $ELEMENT_META_DESCRIPTION;

    /**
     * @var string
     */
    protected $ELEMENT_PAGE_TITLE;

    /**
     * @var string
     */
    protected $ELEMENT_PREVIEW_PICTURE_FILE_ALT;

    /**
     * @var string
     */
    protected $ELEMENT_PREVIEW_PICTURE_FILE_TITLE;

    /**
     * @var string
     */
    protected $ELEMENT_PREVIEW_PICTURE_FILE_NAME;

    /**
     * @var string
     */
    protected $ELEMENT_DETAIL_PICTURE_FILE_ALT;

    /**
     * @var string
     */
    protected $ELEMENT_DETAIL_PICTURE_FILE_TITLE;

    /**
     * @var string
     */
    protected $ELEMENT_DETAIL_PICTURE_FILE_NAME;

    /**
     * @var string
     */
    protected $SECTION_META_TITLE;

    /**
     * @var string
     */
    protected $SECTION_META_KEYWORDS;

    /**
     * @var string
     */
    protected $SECTION_META_DESCRIPTION;

    /**
     * @var string
     */
    protected $SECTION_PAGE_TITLE;

    /**
     * @var string
     */
    protected $SECTION_PREVIEW_PICTURE_FILE_ALT;

    /**
     * @var string
     */
    protected $SECTION_PREVIEW_PICTURE_FILE_TITLE;

    /**
     * @var string
     */
    protected $SECTION_PREVIEW_PICTURE_FILE_NAME;

    /**
     * @var string
     */
    protected $SECTION_DETAIL_PICTURE_FILE_ALT;

    /**
     * @var string
     */
    protected $SECTION_DETAIL_PICTURE_FILE_TITLE;

    /**
     * @var ElementValues
     */
    private $elementValues;

    public function __construct(ElementValues $elementValues, bool $useOriginal = false)
    {
        $this->elementValues = $elementValues;
        parent::__construct($elementValues->getValues(), $useOriginal);
    }

    /**
     * @inheritdoc
     */
    public function getValues(): BaseValues
    {
        return $this->elementValues;
    }

    /**
     * @return string
     */
    public function getMetaTitle(): string
    {
        return (string)$this->ELEMENT_META_TITLE;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setMetaTitle(string $title)
    {
        $this->ELEMENT_META_TITLE = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getMetaKeywords(): string
    {
        return (string)$this->ELEMENT_META_KEYWORDS;
    }

    /**
     * @param string $keywords
     *
     * @return $this
     */
    public function setMetaKeywords(string $keywords)
    {
        $this->ELEMENT_META_KEYWORDS = $keywords;

        return $this;
    }

    /**
     * @return string
     */
    public function getMetaDescription(): string
    {
        return (string)$this->ELEMENT_META_DESCRIPTION;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setMetaDescription(string $description)
    {
        $this->ELEMENT_META_DESCRIPTION = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getPageTitle(): string
    {
        return (string)$this->ELEMENT_PAGE_TITLE;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setPageTitle(string $title)
    {
        $this->ELEMENT_PAGE_TITLE = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getPreviewPictureFileAlt(): string
    {
        return (string)$this->ELEMENT_PREVIEW_PICTURE_FILE_ALT;
    }

    /**
     * @param string $alt
     *
     * @return $this
     */
    public function setPreviewPictureFileAlt(string $alt)
    {
        $this->ELEMENT_PREVIEW_PICTURE_FILE_ALT = $alt;

        return $this;
    }

    /**
     * @return string
     */
    public function getPreviewPictureFileTitle(): string
    {
        return (string)$this->ELEMENT_PREVIEW_PICTURE_FILE_TITLE;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setPreviewPictureFileTitle(string $title)
    {
        $this->ELEMENT_PREVIEW_PICTURE_FILE_TITLE = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getPreviewPictureFileName(): string
    {
        return (string)$this->ELEMENT_PREVIEW_PICTURE_FILE_NAME;
    }

    /**
     * @param string $filename
     *
     * @return $this
     */
    public function setPreviewPictureFileName(string $filename)
    {
        $this->ELEMENT_PREVIEW_PICTURE_FILE_NAME = $filename;

        return $this;
    }

    /**
     * @return string
     */
    public function getDetailPictureFileAlt(): string
    {
        return (string)$this->ELEMENT_DETAIL_PICTURE_FILE_ALT;
    }

    /**
     * @param string $alt
     *
     * @return $this
     */
    public function setDetailPictureFileAlt(string $alt)
    {
        $this->ELEMENT_DETAIL_PICTURE_FILE_ALT = $alt;

        return $this;
    }

    /**
     * @return string
     */
    public function getDetailPictureFileTitle(): string
    {
        return (string)$this->ELEMENT_DETAIL_PICTURE_FILE_TITLE;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setDetailPictureFileTitle(string $title)
    {
        $this->ELEMENT_DETAIL_PICTURE_FILE_TITLE = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getDetailPictureFileName(): string
    {
        return (string)$this->ELEMENT_DETAIL_PICTURE_FILE_NAME;
    }

    /**
     * @param string $filename
     *
     * @return $this
     */
    public function setDetailPictureFileName(string $filename)
    {
        $this->ELEMENT_DETAIL_PICTURE_FILE_NAME = $filename;

        return $this;
    }

    /**
     * @return string
     */
    public function getSectionMetaTitle(): string
    {
        return (string)$this->SECTION_META_TITLE;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setSectionMetaTitle(string $title)
    {
        $this->SECTION_META_TITLE = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getSectionMetaKeywords(): string
    {
        return (string)$this->SECTION_META_KEYWORDS;
    }

    /**
     * @param string $keywords
     *
     * @return $this
     */
    public function setSectionMetaKeywords(string $keywords)
    {
        $this->SECTION_META_KEYWORDS = $keywords;

        return $this;
    }

    /**
     * @return string
     */
    public function getSectionMetaDescription(): string
    {
        return (string)$this->SECTION_META_DESCRIPTION;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setSectionMetaDescription(string $description)
    {
        $this->SECTION_META_DESCRIPTION = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getSectionPageTitle(): string
    {
        return (string)$this->SECTION_PAGE_TITLE;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setSectionPageTitle(string $title)
    {
        $this->SECTION_PAGE_TITLE = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getSectionPreviewPictureFileAlt(): string
    {
        return (string)$this->SECTION_PREVIEW_PICTURE_FILE_ALT;
    }

    /**
     * @param string $alt
     *
     * @return $this
     */
    public function setSectionPreviewPictureFileAlt(string $alt)
    {
        $this->SECTION_PREVIEW_PICTURE_FILE_ALT = $alt;

        return $this;
    }

    /**
     * @return string
     */
    public function getSectionPreviewPictureFileTitle(): string
    {
        return (string)$this->SECTION_PREVIEW_PICTURE_FILE_TITLE;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setSectionPrewviewPictureFileTitle(string $title)
    {
        $this->SECTION_PREVIEW_PICTURE_FILE_TITLE = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getSectionPreviewPictureFileName(): string
    {
        return (string)$this->SECTION_PREVIEW_PICTURE_FILE_NAME;
    }

    /**
     * @param string $filename
     *
     * @return $this
     */
    public function setSectionPreviewPictureFileName(string $filename)
    {
        $this->SECTION_PREVIEW_PICTURE_FILE_NAME = $filename;

        return $this;
    }

    /**
     * @return string
     */
    public function getSectionDetailPictureFileAlt(): string
    {
        return (string)$this->SECTION_DETAIL_PICTURE_FILE_ALT;
    }

    /**
     * @param string $alt
     *
     * @return $this
     */
    public function setSectionDetailPictureFileAlt(string $alt)
    {
        $this->SECTION_DETAIL_PICTURE_FILE_ALT = $alt;

        return $this;
    }

    /**
     * @return string
     */
    public function getSectionDetailPictureFileTitle(): string
    {
        return (string)$this->SECTION_DETAIL_PICTURE_FILE_TITLE;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setSectionDetailPictureFileTitle(string $title)
    {
        $this->SECTION_DETAIL_PICTURE_FILE_TITLE = $title;

        return $this;
    }



}
