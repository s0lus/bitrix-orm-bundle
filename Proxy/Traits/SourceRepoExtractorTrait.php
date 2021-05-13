<?php

namespace Prokl\BitrixOrmBundle\Proxy\Traits;

use Prokl\BitrixOrmBundle\Base\Repository\CdbResultRepository;
use Prokl\BitrixOrmBundle\Base\Repository\D7Repository;
use Prokl\BitrixOrmBundle\Proxy\CacheProxy;
use Prokl\BitrixOrmBundle\Proxy\HydratorProxy;
use LogicException;

/**
 * Trait SourceRepoExtractorTrait
 * @package Prokl\BitrixOrmBundle\Proxy\Traits
 */
trait SourceRepoExtractorTrait
{
    /**
     * @return object|CacheProxy|CdbResultRepository|D7Repository
     */
    abstract public function getRepository();

    /**
     * Возвращает ссылку на исходный репозиторий.
     *
     * @return CdbResultRepository|D7Repository|null
     */
    public function getSourceRepository()
    {
        return $this->extractSourceRepo($this->getRepository());
    }

    /**
     * Возвращает ссылку на исходный репозиторий из всех кеширующих обёрток.
     *
     * @param null|CacheProxy|CdbResultRepository|D7Repository|HydratorProxy $repositoryLikeObject
     *
     * @return CdbResultRepository|D7Repository|null
     */
    protected function extractSourceRepo($repositoryLikeObject)
    {
        if (
            $repositoryLikeObject instanceof CdbResultRepository
            || $repositoryLikeObject instanceof D7Repository
        ) {
            return $repositoryLikeObject;
        }

        if ($repositoryLikeObject instanceof HydratorProxy
            || $repositoryLikeObject instanceof CacheProxy) {
            return $this->extractSourceRepo($repositoryLikeObject->getRepository());
        }

        throw new LogicException(
            sprintf(
                'Got %s, but expected %s or %s or %s or %s',
                get_class($repositoryLikeObject),
                CdbResultRepository::class,
                D7Repository::class,
                HydratorProxy::class,
                CacheProxy::class
            )
        );
    }
}
