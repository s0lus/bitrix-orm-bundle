<?php

namespace Prokl\BitrixOrmBundle\Base\Model\Traits;

trait HasIdTrait
{
    /**
     * @var integer
     */
    protected $ID;

    /**
     * @return integer
     */
    public function getId(): int
    {
        return (int)$this->ID;
    }

    /**
     * @param integer $ID
     *
     * @return $this
     */
    public function setId(int $ID)
    {
        $this->ID = $ID;

        return $this;
    }


}
