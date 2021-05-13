<?php

namespace Prokl\BitrixOrmBundle\Enum;

/**
 * Class ReflectionEnum
 * @package Prokl\BitrixOrmBundle\Enum
 */
class ReflectionEnum
{
    public const ARGUMENT_TYPE_ID      = 'id';
    public const ARGUMENT_TYPE_CODE    = 'code';
    public const ARGUMENT_TYPE_XML_ID  = 'xmlId';
    public const ARGUMENT_TYPE_MODEL   = 'model';
    public const ARGUMENT_TYPE_UNKNOWN = 'unknown';

    public const RETURN_TYPE_MODEL      = 'model';
    public const RETURN_TYPE_COLLECTION = 'collection';
    public const RETURN_TYPE_UKNOWN     = 'unknown';
}
