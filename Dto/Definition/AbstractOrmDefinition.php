<?php

namespace Prokl\BitrixOrmBundle\Dto\Definition;

use Symfony\Component\DependencyInjection\Definition;

/**
 * Class OrmDefinition
 * @package Prokl\BitrixOrmBundle\Dto
 */
abstract class AbstractOrmDefinition
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var Definition
     */
    protected $definition;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return AbstractOrmDefinition
     */
    public function setId(string $id): AbstractOrmDefinition
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return Definition
     */
    public function getDefinition(): Definition
    {
        return $this->definition;
    }

    /**
     * @param Definition $definition
     *
     * @return AbstractOrmDefinition
     */
    public function setDefinition(Definition $definition): AbstractOrmDefinition
    {
        $this->definition = $definition;

        return $this;
    }
}
