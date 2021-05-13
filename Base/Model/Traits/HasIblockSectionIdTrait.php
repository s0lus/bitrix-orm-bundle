<?php

namespace Prokl\BitrixOrmBundle\Base\Model\Traits;

trait HasIblockSectionIdTrait
{
    /**
     * @var integer
     */
    protected $IBLOCK_SECTION_ID;

    /**
     * @return integer
     */
    public function getIblockSectionId(): int
    {
        return (int)$this->IBLOCK_SECTION_ID;
    }

    /**
     * @param integer $sectionId
     *
     * @return $this
     */
    public function setIblockSectionId(int $sectionId)
    {
        $this->IBLOCK_SECTION_ID = $sectionId;

        return $this;
    }


}
