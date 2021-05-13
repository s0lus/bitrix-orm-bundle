<?php

namespace Prokl\BitrixOrmBundle\Base\Model\Iprop;

use Bitrix\Iblock\InheritedProperty\BaseValues;
use Bitrix\Iblock\InheritedProperty\SectionValues;
use Prokl\BitrixOrmBundle\Base\Model\BitrixArrayItemBase;

class IpropSection extends BitrixArrayItemBase implements IpropInterface
{
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
     * @var string
     */
    protected $SECTION_DETAIL_PICTURE_FILE_NAME;

    /**
     * @var SectionValues
     */
    private $sectionValues;

    public function __construct(SectionValues $sectionValues, bool $useOriginal = false)
    {
        $this->sectionValues = $sectionValues;
        parent::__construct($sectionValues->getValues(), $useOriginal);
    }

    /**
     * @inheritdoc
     */
    public function getValues(): BaseValues
    {
        return $this->sectionValues;
    }

    /**
     * @return string
     */
    public function getMetaTitle(): string
    {
        return (string)$this->SECTION_META_TITLE;
    }

    /**
     * @return string
     */
    public function getMetaKeywords(): string
    {
        return (string)$this->SECTION_META_KEYWORDS;
    }

    /**
     * @return string
     */
    public function getMetaDescription(): string
    {
        return (string)$this->SECTION_META_DESCRIPTION;
    }

    /**
     * @return string
     */
    public function getPageTitle(): string
    {
        return (string)$this->SECTION_PAGE_TITLE;
    }

    /**
     * @return string
     */
    public function getPreviewPictureFileAlt(): string
    {
        return (string)$this->SECTION_PREVIEW_PICTURE_FILE_ALT;
    }

    /**
     * @return string
     */
    public function getPreviewPictureFileTitle(): string
    {
        return (string)$this->SECTION_PREVIEW_PICTURE_FILE_TITLE;
    }

    /**
     * @return string
     */
    public function getPreviewPictureFileName(): string
    {
        return (string)$this->SECTION_PREVIEW_PICTURE_FILE_NAME;
    }

    /**
     * @return string
     */
    public function getDetailPictureFileAlt(): string
    {
        return (string)$this->SECTION_DETAIL_PICTURE_FILE_ALT;
    }

    /**
     * @return string
     */
    public function getDetailPictureFileTitle(): string
    {
        return (string)$this->SECTION_DETAIL_PICTURE_FILE_TITLE;
    }

    /**
     * @return string
     */
    public function getDetailPictureFileName(): string
    {
        return (string)$this->SECTION_DETAIL_PICTURE_FILE_NAME;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setMetaTitle(string $title)
    {
        $this->SECTION_META_TITLE = $title;

        return $this;
    }

    /**
     * @param string $keywords
     *
     * @return $this
     */
    public function setMetaKeywords(string $keywords)
    {
        $this->SECTION_META_KEYWORDS = $keywords;

        return $this;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setMetaDescription(string $description)
    {
        $this->SECTION_META_DESCRIPTION = $description;

        return $this;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setPageTitle(string $title)
    {
        $this->SECTION_PAGE_TITLE = $title;

        return $this;
    }

    /**
     * @param string $alt
     *
     * @return $this
     */
    public function setPreviewPictureFileAlt(string $alt)
    {
        $this->SECTION_PREVIEW_PICTURE_FILE_ALT = $alt;

        return $this;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setPreviewPictureFileTitle(string $title)
    {
        $this->SECTION_PREVIEW_PICTURE_FILE_TITLE = $title;

        return $this;
    }

    /**
     * @param string $filename
     *
     * @return $this
     */
    public function setPreviewPictureFilename(string $filename)
    {
        $this->SECTION_PREVIEW_PICTURE_FILE_NAME = $filename;

        return $this;
    }

    /**
     * @param string $alt
     *
     * @return $this
     */
    public function setDetailPictureFileAlt(string $alt)
    {
        $this->SECTION_DETAIL_PICTURE_FILE_ALT = $alt;

        return $this;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setDetailPictureFileTitle(string $title)
    {
        $this->SECTION_DETAIL_PICTURE_FILE_TITLE = $title;

        return $this;
    }

    /**
     * @param string $filename
     *
     * @return $this
     */
    public function setDetailPictureFileName(string $filename)
    {
        $this->SECTION_DETAIL_PICTURE_FILE_NAME = $filename;

        return $this;
    }

}
