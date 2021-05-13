<?php

namespace Prokl\BitrixOrmBundle\Base\Model\Interfaces;

interface HasLeftAndRightMarginsInterface
{
    /**
     * @return integer
     */
    public function getLeftMargin(): int;

    /**
     * @param integer $leftMargin
     *
     * @return $this
     */
    public function setLeftMargin(int $leftMargin);

    /**
     * @return integer
     */
    public function getRightMargin(): int;

    /**
     * @param integer $rightMargin
     *
     * @return $this
     */
    public function setRightMargin(int $rightMargin);
}
