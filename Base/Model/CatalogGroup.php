<?php

namespace Prokl\BitrixOrmBundle\Base\Model;


use Prokl\BitrixOrmBundle\Base\Model\Interfaces\HasActiveInterface;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\HasNameInterface;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\HasSortInterface;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\HasXmlIdInterface;
use Prokl\BitrixOrmBundle\Base\Model\Traits\HasActiveAsStringTrait;
use Prokl\BitrixOrmBundle\Base\Model\Traits\HasIdTrait;
use Prokl\BitrixOrmBundle\Base\Model\Traits\HasSortTrait;
use Prokl\BitrixOrmBundle\Tools\Convertors\BitrixBool;

/**
 * Class CatalogGroup
 *
 * Модель типа цены.
 *
 * @package Prokl\BitrixOrmBundle\Base\\Model
 */
class CatalogGroup extends BitrixArrayItemBase implements
    HasActiveInterface,
    HasXmlIdInterface,
    HasSortInterface,
    HasNameInterface
{
    use HasIdTrait;
    use HasActiveAsStringTrait;
    use HasSortTrait;

    /**
     * @var string Символьный код типа цен, несмотря на нелепое название.
     */
    protected $NAME;

    /**
     * @var string Языкозависимое название типа цены.
     */
    protected $NAME_LANG;

     /**
     * @var boolean
     */
    protected $base;

    public function __construct(array $fields = [], bool $useOriginal = false)
    {
        parent::__construct($fields, $useOriginal);
        $nonInitializedFields = $this->getNonInitializedFields();
        if (isset($nonInitializedFields['BASE'])) {
            $this->setBase(BitrixBool::stringToBool($nonInitializedFields['BASE']));
            unset($nonInitializedFields['BASE']);
            $this->setNonInitializedFields($nonInitializedFields);
        }
    }

    /**
     * @return boolean
     */
    public function isBase(): bool
    {
        return (bool)$this->base;
    }

    /**
     * @param boolean $base
     *
     * @return $this
     */
    public function setBase(bool $base)
    {
        $this->base = $base;

        return $this;
    }

    /**
     * Возвращает языкозависимое название типа цен.
     *
     * @return string
     */
    public function getName(): string
    {
        return (string)$this->NAME_LANG;
    }

    /**
     * Устанавливает языкозависимое название типа цен.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name)
    {
        $this->NAME_LANG = $name;

        return $this;
    }

    /**
     * Возвращает символьный код типа цен.
     *
     * @return string
     */
    public function getXmlId(): string
    {
        return (string)$this->NAME;
    }

    /**
     * Устанавливает символьный код типа цен.
     *
     * @param string $xmlId
     *
     * @return $this
     */
    public function setXmlId(string $xmlId)
    {
        $this->NAME = $xmlId;

        return $this;
    }

}
