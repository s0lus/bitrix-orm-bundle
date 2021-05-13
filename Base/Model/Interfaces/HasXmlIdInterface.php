<?php

namespace Prokl\BitrixOrmBundle\Base\Model\Interfaces;

/**
 * Interface HasXmlIdInterface
 * @package Prokl\BitrixOrmBundle\Base\Model\Interfaces
 */
interface HasXmlIdInterface
{
    /**
     * @return string
     */
    public function getXmlId(): string;

    /**
     * @param string $xmlId
     *
     * @return $this
     */
    public function setXmlId(string $xmlId);
}
