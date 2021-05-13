<?php

namespace Prokl\BitrixOrmBundle\Base\Model\Interfaces;

interface HasListPageUrlInterface
{
    /**
     * @return string
     */
    public function getListPageUrl(): string;

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setListPageUrl(string $url);
}
