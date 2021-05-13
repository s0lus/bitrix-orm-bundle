<?php

namespace Prokl\BitrixOrmBundle\Dto;

/**
 * Class AnnotatedFile
 * @package Prokl\BitrixOrmBundle\Dto
 */
class AnnotatedFile
{
    /**
     * @var string
     */
    protected $path = '';

    /**
     * @var string
     */
    protected $className = '';

    /**
     * @var array
     */
    protected $annotations = [];

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
     * @return AnnotatedFile
     */
    public function setPath(string $path): AnnotatedFile
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @param string $className
     *
     * @return AnnotatedFile
     */
    public function setClassName(string $className): AnnotatedFile
    {
        $this->className = $className;

        return $this;
    }

    /**
     * @return array
     */
    public function getAnnotations(): array
    {
        return $this->annotations;
    }

    /**
     * @param array $annotations
     *
     * @return AnnotatedFile
     */
    public function setAnnotations(array $annotations): AnnotatedFile
    {
        $this->annotations = $annotations;

        return $this;
    }
}
