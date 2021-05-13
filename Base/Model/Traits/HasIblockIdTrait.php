<?php

namespace Prokl\BitrixOrmBundle\Base\Model\Traits;

trait HasIblockIdTrait
{
    /**
     * @var integer
     */
    protected $IBLOCK_ID;

    /**
     * @return integer
     */
    public function getIblockId(): int
    {
        return (int)$this->IBLOCK_ID;
    }

    /**
     * @param integer $iblockId
     *
     * @return $this
     */
    public function setIblockId(int $iblockId)
    {
        $this->IBLOCK_ID = $iblockId;

        return $this;
    }


}
