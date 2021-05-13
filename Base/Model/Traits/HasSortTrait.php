<?php

namespace Prokl\BitrixOrmBundle\Base\Model\Traits;

trait HasSortTrait
{
    /**
     * @var integer
     */
    protected $SORT;

    /**
     * @return integer
     */
    public function getSort(): int
    {
        return (int)$this->SORT;
    }

    /**
     * @param integer $sort
     *
     * @return $this
     */
    public function setSort(int $sort)
    {
        $this->SORT = $sort;

        return $this;
    }


}
