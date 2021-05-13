<?php

namespace Prokl\BitrixOrmBundle\Base\Model;

use DateTimeImmutable;
use Prokl\BitrixOrmBundle\Tools\Convertors\BitrixDateTimeConvert;

/**
 * Class SearchItem
 * @package Prokl\BitrixOrmBundle\Base\Model
 */
class SearchItem extends BitrixArrayItemBase
{
    /**
     * @var string
     */
    protected $DATE_CHANGE;

    /**
     * @var DateTimeImmutable
     */
    protected $dateChange;

    /**
     * @var string
     */
    protected $MODULE_ID;

    /**
     * @var integer
     */
    protected $ITEM_ID;

    /**
     * @var string
     */
    protected $LID;

    /**
     * @var string
     */
    protected $URL;

    /**
     * @var string
     */
    protected $TITLE;

    /**
     * @var string
     */
    protected $BODY;

    /**
     * @var string
     */
    protected $SEARCHABLE_CONTENT;

    /**
     * @var string
     */
    protected $PARAM1;

    /**
     * @var string
     */
    protected $PARAM2;

    /**
     * @var string
     */
    protected $TITLE_FORMATED;

    /**
     * @var string
     */
    protected $BODY_FORMATED;

    /**
     * @var string
     */
    protected $URL_WO_PARAMS;

    /**
     * @return DateTimeImmutable|null
     */
    public function getDateChange()
    {
        if (is_null($this->dateChange) && trim($this->DATE_CHANGE) !== '') {
            $this->dateChange = BitrixDateTimeConvert::bitrixStringDateTimeToDateTimeImmutable(
                trim($this->DATE_CHANGE)
            );
        }

        return $this->dateChange;
    }

    /**
     * @param DateTimeImmutable $dateChange
     *
     * @return $this
     */
    public function setDateChange(DateTimeImmutable $dateChange)
    {
        $this->DATE_CHANGE = BitrixDateTimeConvert::dateTimeImmutableToBitrixDateTime($dateChange);
        $this->dateChange = $dateChange;

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
    public function getItemId(): int
    {
        return (int)$this->ITEM_ID;
    }

    /**
     * @param integer $itemId
     *
     * @return $this
     */
    public function setItemId(int $itemId)
    {
        $this->ITEM_ID = $itemId;

        return $this;
    }

    /**
     * @return string
     */
    public function getLid(): string
    {
        return (string)$this->LID;
    }

    /**
     * @param string $lid
     *
     * @return $this
     */
    public function setLid(string $lid)
    {
        $this->LID = $lid;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return (string)$this->URL;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setUrl(string $url)
    {
        $this->URL = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return (string)$this->TITLE;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle(string $title)
    {
        $this->TITLE = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return (string)$this->BODY;
    }

    /**
     * @param string $body
     *
     * @return $this
     */
    public function setBody(string $body)
    {
        $this->BODY = $body;

        return $this;
    }

    /**
     * @return string
     */
    public function getSearchableContent(): string
    {
        return (string)$this->SEARCHABLE_CONTENT;
    }

    /**
     * @param string $searchableContent
     *
     * @return $this
     */
    public function setSearchableContent(string $searchableContent)
    {
        $this->SEARCHABLE_CONTENT = $searchableContent;

        return $this;
    }

    /**
     * @return string
     */
    public function getParam1(): string
    {
        return (string)$this->PARAM1;
    }

    /**
     * @param string $param1
     *
     * @return $this
     */
    public function setParam1(string $param1)
    {
        $this->PARAM1 = $param1;

        return $this;
    }

    /**
     * @return string
     */
    public function getParam2(): string
    {
        return (string)$this->PARAM2;
    }

    /**
     * @param string $param2
     *
     * @return $this
     */
    public function setParam2(string $param2)
    {
        $this->PARAM2 = $param2;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitleFormated(): string
    {
        return (string)$this->TITLE_FORMATED;
    }

    /**
     * @param string $titleFormated
     *
     * @return $this
     */
    public function setTitleFormated(string $titleFormated)
    {
        $this->TITLE_FORMATED = $titleFormated;

        return $this;
    }

    /**
     * @return string
     */
    public function getBodyFormated(): string
    {
        return (string)$this->BODY_FORMATED;
    }

    /**
     * @param string $bodyFormated
     *
     * @return $this
     */
    public function setBodyFormated(string $bodyFormated)
    {
        $this->BODY_FORMATED = $bodyFormated;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrlWoParams(): string
    {
        return (string)$this->URL_WO_PARAMS;
    }

    /**
     * @param string $urlWoParams
     *
     * @return $this
     */
    public function setUrlWoParams(string $urlWoParams)
    {
        $this->URL_WO_PARAMS = $urlWoParams;

        return $this;
    }

}
