<?php

namespace Prokl\BitrixOrmBundle\Base\Model\Iprop;

use Bitrix\Iblock\InheritedProperty\BaseValues;

interface IpropInterface
{
    /**
     * @return BaseValues
     */
    public function getValues(): BaseValues;

    /**
     * @return string
     */
    public function getMetaTitle(): string;

    /**
     * @return string
     */
    public function getMetaKeywords(): string;

    /**
     * @return string
     */
    public function getMetaDescription(): string;

    /**
     * @return string
     */
    public function getPageTitle(): string;

    /**
     * @return string
     */
    public function getPreviewPictureFileAlt(): string;

    /**
     * @return string
     */
    public function getPreviewPictureFileTitle(): string;

    /**
     * @return string
     */
    public function getPreviewPictureFileName(): string;

    /**
     * @return string
     */
    public function getDetailPictureFileAlt(): string;

    /**
     * @return string
     */
    public function getDetailPictureFileTitle(): string;

    /**
     * @return string
     */
    public function getDetailPictureFileName(): string;

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setMetaTitle(string $title);

    /**
     * @param string $keywords
     *
     * @return $this
     */
    public function setMetaKeywords(string $keywords);

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setMetaDescription(string $description);

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setPageTitle(string $title);

    /**
     * @param string $alt
     *
     * @return $this
     */
    public function setPreviewPictureFileAlt(string $alt);

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setPreviewPictureFileTitle(string $title);

    /**
     * @param string $filename
     *
     * @return $this
     */
    public function setPreviewPictureFilename(string $filename);

    /**
     * @param string $alt
     *
     * @return $this
     */
    public function setDetailPictureFileAlt(string $alt);

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setDetailPictureFileTitle(string $title);

    /**
     * @param string $filename
     *
     * @return $this
     */
    public function setDetailPictureFileName(string $filename);
}
