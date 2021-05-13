<?php

namespace Prokl\BitrixOrmBundle\Base\Model\Traits;

trait HasCodeTrait
{
    /**
     * @var string
     */
    protected $CODE;

    /**
     * @return string
     */
    public function getCode(): string
    {
        return (string)$this->CODE;
    }

    /**
     * @param string $code
     *
     * @return $this
     */
    public function setCode(string $code)
    {
        $this->CODE = $code;

        return $this;
    }


}
