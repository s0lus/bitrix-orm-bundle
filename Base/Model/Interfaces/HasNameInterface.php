<?php

namespace Prokl\BitrixOrmBundle\Base\Model\Interfaces;

interface HasNameInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name);
}
