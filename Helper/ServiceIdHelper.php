<?php

namespace Prokl\BitrixOrmBundle\Helper;

/**
 * Class ServiceIdHelper
 * @package Prokl\BitrixOrmBundle\Helper
 */
class ServiceIdHelper
{
    /**
     * @param string $className
     *
     * @return string
     */
    public static function getFactoryServiceId(string $className): string
    {
        return \sprintf('bitrix_orm.%s_factory', static::prepareClassName($className));
    }

    /**
     * @param string $className
     *
     * @return string
     */
    public static function getRepositoryServiceId(string $className): string
    {
        return \sprintf('bitrix_orm.%s_repository', static::prepareClassName($className));
    }

    /**
     * @param string $className
     *
     * @return string
     */
    public static function getCacheServiceId(string $className): string
    {
        return \sprintf('bitrix_orm.%s_cache', static::prepareClassName($className));
    }

    /**
     * @param string $className
     *
     * @return string
     */
    public static function getCacheProxyServiceId(string $className): string
    {
        return \sprintf('bitrix_orm.%s_cache_proxy', static::prepareClassName($className));
    }

    /**
     * @param string $className
     *
     * @return string
     */
    public static function getBitrixCacheServiceId(string $className): string
    {
        return \sprintf('bitrix_orm.%s_bitrix_cache', static::prepareClassName($className));
    }

    /**
     * @param string $className
     *
     * @return string
     */
    public static function getBitrixCacheProxyServiceId(string $className): string
    {
        return \sprintf('bitrix_orm.%s_bitrix_cache_proxy', static::prepareClassName($className));
    }

    /**
     * @param string $className
     *
     * @return string
     */
    public static function getHydratorServiceId(string $className): string
    {
        return \sprintf('bitrix_orm.%s_hydrator', static::prepareClassName($className));
    }

    /**
     * @param string $className
     *
     * @return string
     */
    public static function getHydratorProxyServiceId(string $className): string
    {
        return \sprintf('bitrix_orm.%s_hydrator_proxy', static::prepareClassName($className));
    }

    /**
     * @param string $className
     *
     * @return string
     */
    public static function getDataManagerServiceId(string $className): string
    {
        return \sprintf('bitrix_orm.%s_data_manager', static::prepareClassName($className));
    }

    /**
     * @param string $className
     *
     * @return string
     */
    protected static function prepareClassName(string $className): string
    {
        return \mb_strtolower(\str_replace('\\', '_', $className));
    }
}
