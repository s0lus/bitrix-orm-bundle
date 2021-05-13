<?php

namespace Prokl\BitrixOrmBundle\Dto;

/**
 * Class ReflectionData
 * @package Prokl\BitrixOrmBundle\Dto
 */
class ReflectionData
{
    /**
     * @var string
     */
    protected $argumentType;

    /**
     * @var string
     */
    protected $returnType;

    /**
     * @return string
     */
    public function getArgumentType(): string
    {
        return $this->argumentType;
    }

    /**
     * @param string $argumentType
     *
     * @return ReflectionData
     */
    public function setArgumentType(string $argumentType): ReflectionData
    {
        $this->argumentType = $argumentType;

        return $this;
    }

    /**
     * @return string
     */
    public function getReturnType(): string
    {
        return $this->returnType;
    }

    /**
     * @param string $returnType
     *
     * @return ReflectionData
     */
    public function setReturnType(string $returnType): ReflectionData
    {
        $this->returnType = $returnType;

        return $this;
    }
}
