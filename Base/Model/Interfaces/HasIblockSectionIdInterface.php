<?php

namespace Prokl\BitrixOrmBundle\Base\Model\Interfaces;

interface HasIblockSectionIdInterface
{
    /**
     * @return integer
     */
    public function getIblockSectionId(): int;

    /**
     * @param integer $sectionId
     *
     * @return $this
     */
    public function setIblockSectionId(int $sectionId);
}
