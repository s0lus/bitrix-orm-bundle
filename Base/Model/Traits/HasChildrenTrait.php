<?php

namespace Prokl\BitrixOrmBundle\Base\Model\Traits;

use Prokl\BitrixOrmBundle\Base\Collection\CdbResultItemCollection;

/**
 * Trait HasChildrenTrait
 * @package Prokl\BitrixOrmBundle\Base\\Model\Traits
 */
trait HasChildrenTrait
{
    /**
     * @var CdbResultItemCollection
     */
    protected $children;

    /**
     * @return CdbResultItemCollection
     */
    public function getChildren(): CdbResultItemCollection
    {
        if (!$this->children) {
            $this->children = new CdbResultItemCollection();
        }

        return $this->children;
    }

    /**
     * @param CdbResultItemCollection $children
     *
     * @return $this
     */
    public function setChildren(CdbResultItemCollection $children): self
    {
        $this->children = $children;

        return $this;
    }


}
