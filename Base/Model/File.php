<?php

namespace Prokl\BitrixOrmBundle\Base\Model;

use CFile;
use CIBlock;
use COption;
use Prokl\BitrixOrmBundle\Base\Enum\Module;
use Prokl\BitrixOrmBundle\Tools\Convertors\BitrixBool;

/**
 * Class File
 *
 * Представление результата CFile::GetFileArray , которое, вообще-то, может быть и файлом, и картинкой.
 *
 * @package Prokl\BitrixOrmBundle\Base\\Model
 */
class File extends BitrixArrayItemBase
{
    /**
     * @var integer
     */
    protected $ID;

    /**
     * @var string
     */
    protected $TIMESTAMP_X;

    /**
     * @var string
     */
    protected $MODULE_ID;

    /**
     * @var integer
     */
    protected $HEIGHT;

    /**
     * @var integer
     */
    protected $WIDTH;

    /**
     * @var integer
     */
    protected $FILE_SIZE;

    /**
     * @var string
     */
    protected $CONTENT_TYPE;

    /**
     * @var string
     */
    protected $SUBDIR;

    /**
     * @var string
     */
    protected $FILE_NAME;

    /**
     * @var string
     */
    protected $ORIGINAL_NAME;

    /**
     * @var string
     */
    protected $DESCRIPTION;

    /**
     * @var mixed
     */
    protected $HANDLER_ID;

    /**
     * @var string
     */
    protected $EXTERNAL_ID;

    /**
     * @var string
     */
    protected $src;

    /**
     * @var integer
     */
    protected $oldFileId = 0;

     /**
     * @var boolean
     */
    protected $delete = false;

    /**
     * Возвращает массив описания файла от CFile::MakeFileArray для нового файла или его числовой id для уже
     * существующего файла, или массив описания файла особого вида для модуля "Информационных блоков". Метод
     * предназначен для получения корректного значения при добавлении или обновлении различных сущностей с полями или
     * свойствами типа "Файл".
     *
     * @return array|integer
     * @see CFile::MakeFileArray
     */
    public function getValueForSaving()
    {
        /**
         * Только у модуля "Информационных блоков" при отключённом модуле "Документооборот" отправка ID прикреплённого
         * файла для "картинки для анонса" или для "детальной картинки" или в свойстве типа "Файл" приводит к ошибке
         * сохранения файла(сообщения с кодами IBLOCK_ERR_PREVIEW_PICTURE, IBLOCK_ERR_DETAIL_PICTURE и
         * IBLOCK_ERR_FILE_PROPERTY соответственно).
         *
         * Следует заменить ID файла на массив описания файла особого вида, который может быть сформирован только
         * вспомогательным методом CIBlock::makeFileArray().
         */
        if (Module::IBLOCK === $this->getModuleId() && $this->getId() > 0) {
            return CIBlock::makeFileArray($this->getId());
        }

        if ($this->getId() > 0) {
            return $this->getId();
        }

        $fileArray = $this->getMakeFileArray();

        if (trim($this->getOriginalName()) !== '') {
            // Приоритет у оригинального имени файла.
            $fileArray['name'] = trim($this->getOriginalName());
        } elseif (trim($this->getFilename()) !== '') {
            $fileArray['name'] = trim($this->getFilename());
        }

        if (trim($this->getModuleId()) !== '') {
            $fileArray['MODULE_ID'] = $this->getModuleId();
        }

        if (trim($this->getContentType()) !== '') {
            $fileArray['type'] = trim($this->getContentType());
        }
        if (trim($this->getDescription()) !== '') {
            $fileArray['description'] = trim($this->getDescription());
        }

        if ($this->isDelete()) {
            $fileArray['del'] = BitrixBool::TRUE;
        }

        if ($this->getOldFileId() > 0) {
            $fileArray['old_file'] = $this->getOldFileId();
        }

        return $fileArray;
    }

    /**
     * @return integer
     */
    public function getId(): int
    {
        return (int)$this->ID;
    }

    /**
     * @param integer $id
     *
     * @return $this
     */
    public function setId(int $id)
    {
        $this->ID = $id;

        return $this;
    }

    /**
     * Возвращает путь к файлу от корня сайта.
     *
     * @return string
     */
    public function getSrc(): string
    {
        if (is_null($this->src)) {
            //TODO Может быть проблема двойных слешей с картинками из HL-блоков
            if ($this->getId() === 0) {
                $this->src = '';

                return $this->src;
            }
            $this->src = sprintf(
                '/%s/%s/%s',
                COption::GetOptionString('main', 'upload_dir', 'upload'),
                $this->getSubdir(),
                $this->getFilename()
            );
        }

        return $this->src;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getSrc();
    }

    /**
     * @param string $src
     *
     * @return $this
     */
    public function setSrc(string $src)
    {
        $this->src = $src;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubdir(): string
    {
        return (string)$this->SUBDIR;
    }

    /**
     * @param string $subdir
     *
     * @return $this
     */
    public function setSubdir(string $subdir)
    {
        $this->SUBDIR = $subdir;

        return $this;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return (string)$this->FILE_NAME;
    }

    /**
     * @param string $filename
     *
     * @return $this
     */
    public function setFilename(string $filename)
    {
        $this->FILE_NAME = $filename;

        return $this;
    }

    /**
     * @return integer
     */
    public function getHeight(): int
    {
        return (int)$this->HEIGHT;
    }

    /**
     * @param integer $height Высота.
     *
     * @return $this
     */
    public function setHeight(int $height)
    {
        $this->HEIGHT = $height;

        return $this;
    }

    /**
     * @return integer
     */
    public function getWidth(): int
    {
        return (int)$this->WIDTH;
    }

    /**
     * @param integer $width
     *
     * @return $this
     */
    public function setWidth(int $width)
    {
        $this->WIDTH = $width;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return (string)$this->DESCRIPTION;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription(string $description): File
    {
        $this->DESCRIPTION = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getTimestampX(): string
    {
        return (string)$this->TIMESTAMP_X;
    }

    /**
     * @param string $timestampX
     *
     * @return $this
     */
    public function setTimestampX(string $timestampX)
    {
        $this->TIMESTAMP_X = $timestampX;

        return $this;
    }

    /**
     * @return string
     */
    public function getModuleId(): string
    {
        return (string)$this->MODULE_ID;
    }

    /**
     * @param string $moduleId
     *
     * @return $this
     */
    public function setModuleId(string $moduleId)
    {
        $this->MODULE_ID = $moduleId;

        return $this;
    }

    /**
     * @return integer
     */
    public function getFileSize(): int
    {
        return (int)$this->FILE_SIZE;
    }

    /**
     * @param integer $fileSize
     *
     * @return $this
     */
    public function setFileSize(int $fileSize)
    {
        $this->FILE_SIZE = $fileSize;

        return $this;
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return (string)$this->CONTENT_TYPE;
    }

    /**
     * @param string $contentType
     *
     * @return $this
     */
    public function setContentType(string $contentType)
    {
        $this->CONTENT_TYPE = $contentType;

        return $this;
    }

    /**
     * @return string
     */
    public function getOriginalName(): string
    {
        return (string)$this->ORIGINAL_NAME;
    }

    /**
     * @param string $originalName
     *
     * @return $this
     */
    public function setOriginalName(string $originalName)
    {
        $this->ORIGINAL_NAME = $originalName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getHandlerId()
    {
        return $this->HANDLER_ID;
    }

    /**
     * @param mixed $handlerId
     *
     * @return $this
     */
    public function setHandlerId($handlerId)
    {
        $this->HANDLER_ID = $handlerId;

        return $this;
    }

    /**
     * @return string
     */
    public function getExternalId(): string
    {
        return (string)$this->EXTERNAL_ID;
    }

    /**
     * @param string $externalId
     *
     * @return $this
     */
    public function setExternalId(string $externalId)
    {
        $this->EXTERNAL_ID = $externalId;

        return $this;
    }

    /**
     * @return integer
     */
    public function getOldFileId(): int
    {
        return $this->oldFileId;
    }

    /**
     * @param integer $oldFileId
     *
     * @return $this
     */
    public function setOldFileId(int $oldFileId)
    {
        $this->oldFileId = $oldFileId;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isDelete(): bool
    {
        return $this->delete;
    }

    /**
     * @param boolean $delete
     *
     * @return $this
     */
    public function setDelete(bool $delete)
    {
        $this->delete = $delete;

        return $this;
    }

    /**
     * @return array|bool|null
     */
    protected function getMakeFileArray()
    {
        return CFile::MakeFileArray($this->getSrc());
    }
}
