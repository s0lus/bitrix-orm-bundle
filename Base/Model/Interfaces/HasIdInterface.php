<?php

namespace Prokl\BitrixOrmBundle\Base\Model\Interfaces;

interface HasIdInterface
{
    /**
     * @return integer
     */
    public function getId(): int;

    /**
     * @param integer $ID
     *
     * @return $this
     */
    public function setId(int $ID);
}
