<?php

namespace Prokl\BitrixOrmBundle\Base\Model\Interfaces;

interface HasActiveInterface
{
    /**
     * @return boolean
     */
    public function isActive(): bool;

    /**
     * @param boolean $active
     *
     * @return $this
     */
    public function setActive(bool $active);
}
