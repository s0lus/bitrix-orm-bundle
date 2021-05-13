<?php

namespace Prokl\BitrixOrmBundle\Base\Model;

use DateTimeImmutable;

class Price extends D7Item
{
    /**
     * @var integer
     */
    protected $PRODUCT_ID;

    /**
     * @var integer
     */
    protected $EXTRA_ID;

    /**
     * @var integer
     */
    protected $CATALOG_GROUP_ID;

    /**
     * @var float
     */
    protected $PRICE;

    /**
     * @var string
     */
    protected $CURRENCY;

    /**
     * @var DateTimeImmutable
     */
    protected $TIMESTAMP_X;

    /**
     * @var integer
     */
    protected $QUANTITY_FROM;

    /**
     * @var integer
     */
    protected $QUANTITY_TO;

    /**
     * @var string
     */
    protected $TMP_ID;

    /**
     * @var float
     */
    protected $PRICE_SCALE;

    /**
     * @return integer
     */
    public function getProductId(): int
    {
        return (int)$this->PRODUCT_ID;
    }

    /**
     * @param integer $productId
     *
     * @return $this
     */
    public function setProductId(int $productId)
    {
        $this->PRODUCT_ID = $productId;

        return $this;
    }

    /**
     * @return integer
     */
    public function getExtraId(): int
    {
        return (int)$this->EXTRA_ID;
    }

    /**
     * @param integer $extraId
     *
     * @return $this
     */
    public function setExtraId(int $extraId)
    {
        $this->EXTRA_ID = $extraId;

        return $this;
    }

    /**
     * @return integer
     */
    public function getCatalogGroupId(): int
    {
        return (int)$this->CATALOG_GROUP_ID;
    }

    /**
     * @param integer $catalogGroupId
     *
     * @return $this
     */
    public function setCatalogGroupId(int $catalogGroupId)
    {
        $this->CATALOG_GROUP_ID = $catalogGroupId;

        return $this;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return (float)$this->PRICE;
    }

    /**
     * @param float $price
     *
     * @return $this
     */
    public function setPrice(float $price)
    {
        $this->PRICE = $price;

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return (string)$this->CURRENCY;
    }

    /**
     * @param string $currency
     *
     * @return $this
     */
    public function setCurrency(string $currency)
    {
        $this->CURRENCY = $currency;

        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getTimestampX()
    {
        return $this->TIMESTAMP_X;
    }

    /**
     * @param DateTimeImmutable $timestampX
     *
     * @return $this
     */
    public function setTimestampX(DateTimeImmutable $timestampX)
    {
        $this->TIMESTAMP_X = $timestampX;

        return $this;
    }

    /**
     * @return integer
     */
    public function getQuantityFrom(): int
    {
        return (int)$this->QUANTITY_FROM;
    }

    /**
     * @param integer $quantityFrom
     *
     * @return $this
     */
    public function setQuantityFrom(int $quantityFrom)
    {
        $this->QUANTITY_FROM = $quantityFrom;

        return $this;
    }

    /**
     * @return integer
     */
    public function getQuantityTo(): int
    {
        return (int)$this->QUANTITY_TO;
    }

    /**
     * @param integer $quantityTo
     *
     * @return $this
     */
    public function setQuantityTo(int $quantityTo)
    {
        $this->QUANTITY_TO = $quantityTo;

        return $this;
    }

    /**
     * @return string
     */
    public function getTmpId(): string
    {
        return (string)$this->TMP_ID;
    }

    /**
     * @param string $tmpId
     *
     * @return $this
     */
    public function setTmpId(string $tmpId)
    {
        $this->TMP_ID = $tmpId;

        return $this;
    }

    /**
     * @return float
     */
    public function getPriceScale(): float
    {
        return (float)$this->PRICE_SCALE;
    }

    /**
     * @param float $priceScale
     *
     * @return $this
     */
    public function setPriceScale(float $priceScale)
    {
        $this->PRICE_SCALE = $priceScale;

        return $this;
    }
}
