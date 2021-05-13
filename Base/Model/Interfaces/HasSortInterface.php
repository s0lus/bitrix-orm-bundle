<?php

namespace Prokl\BitrixOrmBundle\Base\Model\Interfaces;

interface HasSortInterface
{
    /**
     * @return integer
     */
    public function getSort(): int;

    /**
     * @param integer $sort
     *
     * @return $this
     */
    public function setSort(int $sort);
}
