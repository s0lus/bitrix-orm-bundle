<?php

namespace Prokl\BitrixOrmBundle\Base\Model\Interfaces;

interface HasCodeInterface
{
    /**
     * @return string
     */
    public function getCode(): string;

    /**
     * @param string $code
     *
     * @return $this
     */
    public function setCode(string $code);
}
