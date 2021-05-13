<?php

namespace Prokl\BitrixOrmBundle\Base\Model\Traits;

trait HasXmlIdTrait
{
    /**
     * @var string
     */
    protected $XML_ID;

    /**
     * @return string
     */
    public function getXmlId(): string
    {
        return (string)$this->XML_ID;
    }

    /**
     * @param string $xmlId
     *
     * @return $this
     */
    public function setXmlId(string $xmlId)
    {
        $this->XML_ID = $xmlId;

        return $this;
    }


}
