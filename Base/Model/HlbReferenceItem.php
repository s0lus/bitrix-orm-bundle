<?php

namespace Prokl\BitrixOrmBundle\Base\Model;


use Prokl\BitrixOrmBundle\Base\Model\Interfaces\FileRepositoryAwareInterface;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\HasNameInterface;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\HasSortInterface;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\HasXmlIdInterface;
use Prokl\BitrixOrmBundle\Base\Model\Traits\FileRepositoryAwareTrait;

/**
 * Class HlbReferenceItem
 *
 * Модель элемента HL-блока, используемого в свойстве типа "Справочник".
 *
 * @package Prokl\BitrixOrmBundle\Base\\Model
 */
class HlbReferenceItem extends HlbItemBase implements
    HasXmlIdInterface,
    HasSortInterface,
    HasNameInterface,
    FileRepositoryAwareInterface
{
    use FileRepositoryAwareTrait;

    /**
     * @var string
     */
    protected $UF_NAME;

    /**
     * @var string
     */
    protected $UF_LINK;

    /**
     * @var string
     */
    protected $UF_DESCRIPTION;

    /**
     * @var string
     */
    protected $UF_FULL_DESCRIPTION;

    /**
     * @var integer
     */
    protected $UF_SORT;

    /**
     * @var string
     */
    protected $UF_XML_ID;

    /**
     * @var int|array
     */
    protected $UF_FILE;

    /**
     * @var File
     */
    protected $file;

    //TODO UF_DEF типа "Да/Нет", в котором хранится 1 или 0, а не Y/N

    /**
     * @return string
     */
    public function getLink(): string
    {
        return (string)$this->UF_LINK;
    }

    /**
     * @param string $link
     *
     * @return $this
     */
    public function setLink(string $link)
    {
        $this->UF_LINK = $link;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return (string)$this->UF_DESCRIPTION;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription(string $description)
    {
        $this->UF_DESCRIPTION = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getFullDescription(): string
    {
        return (string)$this->UF_FULL_DESCRIPTION;
    }

    /**
     * @param string $fullDescription
     *
     * @return $this
     */
    public function setFullDescription(string $fullDescription)
    {
        $this->UF_FULL_DESCRIPTION = $fullDescription;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return (string)$this->UF_NAME;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name)
    {
        $this->UF_NAME = $name;

        return $this;
    }

    /**
     * @return integer
     */
    public function getSort(): int
    {
        return (int)$this->UF_SORT;
    }

    /**
     * @param integer $sort
     *
     * @return $this
     */
    public function setSort(int $sort)
    {
        $this->UF_SORT = $sort;

        return $this;
    }

    /**
     * @return string
     */
    public function getXmlId(): string
    {
        return (string)$this->UF_XML_ID;
    }

    /**
     * @param string $xmlId
     *
     * @return $this
     */
    public function setXmlId(string $xmlId)
    {
        $this->UF_XML_ID = $xmlId;

        return $this;
    }

    /**
     * @return integer
     */
    public function getFileId(): int
    {
        return $this->getFile()->getId();
    }

    /**
     * @param integer $fileId
     *
     * @return $this
     */
    public function setFileId(int $fileId)
    {
        $this->UF_FILE = $fileId;
        $this->file = null;

        return $this;
    }

    /**
     * @return File
     */
    public function getFile(): File
    {
        if (is_null($this->file)) {
            $this->file = (self::getFileRepository())->getFileById((int)$this->UF_FILE);
        }

        return $this->file;
    }

    /**
     * @param File $file
     *
     * @return $this
     */
    public function setFile(File $file)
    {
        $this->file = $file;
        $this->UF_FILE = $file->getValueForSaving();

        return $this;
    }

}
