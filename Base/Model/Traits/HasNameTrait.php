<?php

namespace Prokl\BitrixOrmBundle\Base\Model\Traits;

trait HasNameTrait
{
    /**
     * @var string
     */
    protected $NAME;

    /**
     * @return string
     */
    public function getName(): string
    {
        return (string)$this->NAME;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name)
    {
        $this->NAME = $name;

        return $this;
    }


}
