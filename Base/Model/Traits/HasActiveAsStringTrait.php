<?php

namespace Prokl\BitrixOrmBundle\Base\Model\Traits;

use Prokl\BitrixOrmBundle\Tools\Convertors\BitrixBool;

/**
 * Trait HasActiveAsStringTrait
 * @package Prokl\BitrixOrmBundle\Base\Model\Traits
 */
trait HasActiveAsStringTrait
{
    /**
     * @var string BitrixBool
     */
    protected $ACTIVE;

    /**
     * @return boolean
     */
    public function isActive(): bool
    {
        /**
         * Предотвращение \InvalidArgumentException
         * от BitrixBool::stringToBool()
         */
        if (is_null($this->ACTIVE)) {
            return false;
        }

        return BitrixBool::stringToBool((string)$this->ACTIVE);
    }

    /**
     * @param boolean $active
     *
     * @return $this
     */
    public function setActive(bool $active)
    {
        $this->ACTIVE = BitrixBool::boolToString($active);

        return $this;
    }
}
