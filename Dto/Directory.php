<?php

namespace Prokl\BitrixOrmBundle\Dto;

/**
 * Class Directory
 * @package Prokl\BitrixOrmBundle\Dto
 */
class Directory
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $path;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Directory
     */
    public function setName(string $name): Directory
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return Directory
     */
    public function setPath(string $path): Directory
    {
        $this->path = $path;

        return $this;
    }
}
