<?php

namespace Prokl\BitrixOrmBundle\Base\Model\Interfaces;

interface HasIblockIdInterface
{
    /**
     * @return integer
     */
    public function getIblockId(): int;

    /**
     * @param integer $iblockId
     *
     * @return $this
     */
    public function setIblockId(int $iblockId);
}
