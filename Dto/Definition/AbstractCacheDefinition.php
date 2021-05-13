<?php

namespace Prokl\BitrixOrmBundle\Dto\Definition;

/**
 * Class AbstractCacheDefinition
 * @package Prokl\BitrixOrmBundle\Dto\Definition
 */
abstract class AbstractCacheDefinition extends AbstractOrmDefinition
{
    /**
     * @var array
     */
    protected $excludedMethods;

    /**
     * @return array
     */
    public function getExcludedMethods(): array
    {
        return $this->excludedMethods;
    }

    /**
     * @param array $excludedMethods
     *
     * @return AbstractCacheDefinition
     */
    public function setExcludedMethods(array $excludedMethods): AbstractCacheDefinition
    {
        $this->excludedMethods = $excludedMethods;

        return $this;
    }
}
