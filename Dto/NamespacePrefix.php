<?php

namespace Prokl\BitrixOrmBundle\Dto;

/**
 * Class NamespacePrefix
 * @package Prokl\BitrixOrmBundle\Dto
 */
class NamespacePrefix
{
    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var string
     */
    protected $dir;

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * @param string $prefix
     *
     * @return NamespacePrefix
     */
    public function setPrefix(string $prefix): NamespacePrefix
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * @return string
     */
    public function getDir(): string
    {
        return $this->dir;
    }

    /**
     * @param string $dir
     *
     * @return NamespacePrefix
     */
    public function setDir(string $dir): NamespacePrefix
    {
        $this->dir = $dir;

        return $this;
    }
}
