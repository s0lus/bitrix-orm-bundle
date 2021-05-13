<?php

namespace Prokl\BitrixOrmBundle\Base\Model\Traits;

trait HasLeftAndRightMarginsTrait
{
    /**
     * @var integer
     */
    protected $LEFT_MARGIN = 0;

    /**
     * @var integer
     */
    protected $RIGHT_MARGIN = 0;

    /**
     * @inheritDoc
     */
    public function getLeftMargin(): int
    {
        return (int)$this->LEFT_MARGIN;
    }

    /**
     * @inheritDoc
     */
    public function setLeftMargin(int $leftMargin)
    {
        $this->LEFT_MARGIN = $leftMargin;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRightMargin(): int
    {
        return (int)$this->RIGHT_MARGIN;
    }

    /**
     * @inheritDoc
     */
    public function setRightMargin(int $rightMargin)
    {
        $this->RIGHT_MARGIN = $rightMargin;

        return $this;
    }
}
