<?php

namespace Prokl\BitrixOrmBundle\Base\Model\Traits;

trait HasActiveAsIntegerTrait
{
    /**
     * @var int as bool (1/0)
     */
    protected $UF_ACTIVE;

    /**
     * @return boolean
     */
    public function isActive(): bool
    {
        return (bool)$this->UF_ACTIVE;
    }

    /**
     * @param boolean $active
     *
     * @return $this
     */
    public function setActive(bool $active)
    {
        $this->UF_ACTIVE = (int)$active;

        return $this;
    }
}
