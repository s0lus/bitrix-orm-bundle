<?php

namespace Prokl\BitrixOrmBundle\Base\Query;

use InvalidArgumentException;

/**
 * Class QueryBase
 *
 * @internal Классы этого типа не умеют создавать ни коллекцию, ни объект. Они оборачивает API Битрикса, чтобы на входе
 * были объекты BitrixArrayItemInterface, а на выходе результаты типа \Bitrix\Main\Entity\Result . Или же на входе
 * настройка конфигурации запроса, а на выходе чистые ресурсы ответа из БД: \CDBResult или \Bitrix\Main\DB\Result .
 *
 * @package Prokl\BitrixOrmBundle\Base\\Query
 */
abstract class QueryBase
{
    /**
     * @var array
     */
    protected $select = [];

    /**
     * @var array
     */
    protected $filter = [];

    /**
     * @var array
     */
    protected $group = [];

    /**
     * @var array
     */
    protected $order = [];

    /**
     * @var integer
     */
    protected $offset = 0;

    /**
     * @var integer
     */
    protected $limit = 0;

    /**
     * @return array
     */
    public function getSelect(): array
    {
        return $this->select;
    }

    /**
     * @param array $select
     *
     * @return $this
     */
    public function setSelect(array $select)
    {
        $this->select = $select;

        return $this;
    }

    /**
     * @return array
     */
    public function getFilter(): array
    {
        return $this->filter;
    }

    /**
     * @param array $filter Фильтр.
     *
     * @return $this
     */
    public function setFilter(array $filter): self
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function setFilterParameter(string $name, $value)
    {
        if (trim($name) === '') {
            throw new InvalidArgumentException(
                'Empty parameter name specified'
            );
        }

        $this->filter[$name] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getGroup(): array
    {
        return $this->group;
    }

    /**
     * @param array $group
     *
     * @return $this
     */
    public function setGroup(array $group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * @return array
     */
    public function getOrder(): array
    {
        return $this->order;
    }

    /**
     * @param array $order
     *
     * @return $this
     */
    public function setOrder(array $order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return integer
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @param integer $offset
     *
     * @return $this
     */
    public function setOffset(int $offset)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @return integer
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param integer $limit
     *
     * @return $this
     */
    public function setLimit(int $limit)
    {
        $this->limit = $limit;

        return $this;
    }

}
