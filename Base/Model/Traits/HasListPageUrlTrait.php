<?php

namespace Prokl\BitrixOrmBundle\Base\Model\Traits;

trait HasListPageUrlTrait
{
    /**
     * @var string
     */
    protected $LIST_PAGE_URL;

    /**
     * @return string
     */
    public function getListPageUrl(): string
    {
        return (string)$this->LIST_PAGE_URL;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setListPageUrl(string $url)
    {
        $this->LIST_PAGE_URL = $url;

        return $this;
    }


}
