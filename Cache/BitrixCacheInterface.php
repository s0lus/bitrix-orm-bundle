<?php

namespace Prokl\BitrixOrmBundle\Cache;

use Prokl\BitrixOrmBundle\Base\Model\Interfaces\HasIdInterface;

/**
 * Interface TagCacheInterface
 * @package Prokl\BitrixOrmBundle\Cache
 */
interface BitrixCacheInterface extends CacheInterface
{
    /**
     * @return string
     */
    public function getTag(): string;

    /**
     * @param integer         $id
     * @param string|null $tag
     *
     * @return string
     */
    public function getIdTag(int $id, string $tag = null): string;

    /**
     * @param string      $code
     * @param string|null $tag
     *
     * @return string
     */
    public function getCodeTag(string $code, string $tag = null): string;

    /**
     * @param string      $xmlId
     * @param string|null $tag
     *
     * @return string
     */
    public function getXmlIdTag(string $xmlId, string $tag = null): string;

    /**
     * @param HasIdInterface $item
     *
     * @return array
     */
    public function getObjectTags(HasIdInterface $item): array;

    /**
     * @param integer $id
     *
     * @return string
     */
    public function getCollectionIdTag(int $id): string;
}
