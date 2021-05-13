<?php

namespace Prokl\BitrixOrmBundle\Tools\Convertors;

use InvalidArgumentException;

/**
 * Class BitrixBool
 * @package Prokl\BitrixOrmBundle\Tools\Convertors
 */
class BitrixBool
{
    public const FALSE = 'N';

    public const TRUE = 'Y';

    /**
     * Конвертирует bool в строку Y|N
     *
     * @param boolean $bool
     *
     * @return string
     */
    public static function boolToString(bool $bool): string
    {
        return $bool ? self::TRUE : self::FALSE;
    }

    /**
     * Конвертирует строку Y|N в bool.
     *
     * @param string $string Строка.
     *
     * @return boolean
     */
    public static function stringToBool(string $string): bool
    {
        if (self::TRUE !== $string && self::FALSE !== $string && '' !== $string) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected `%s`, `%s` or ``(empty string), but `%s` is given',
                    self::TRUE,
                    self::FALSE,
                    trim($string)
                )
            );
        }

        return self::TRUE === $string;
    }
}
