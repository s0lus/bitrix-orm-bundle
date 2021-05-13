<?php

namespace Prokl\BitrixOrmBundle\Tools\Convertors;

use Bitrix\Main\Result;

/**
 * Class BitrixResult
 * @package Prokl\BitrixOrmBundle\Tools\Convertors
 */
class BitrixResult
{
    /**
     * Возвращает строку с перечислением ошибок для результата Bitrix\Main\Result
     *
     * @param Result $result
     *
     * @param $separator
     *
     * @return string
     */
    public static function getErrorString(Result $result, $separator = '; '): string
    {
        return implode($separator, $result->getErrorMessages());
    }
}
