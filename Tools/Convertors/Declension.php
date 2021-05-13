<?php

namespace Prokl\BitrixOrmBundle\Tools\Convertors;

/**
 * Class Declension
 * @package Prokl\BitrixOrmBundle\Tools\Convertors
 */
class Declension
{
    /**
     * Возвращает склонение слова для числа.
     *
     * @param float $number
     * @param string $one
     * @param string $two
     * @param string $five
     * @param boolean $onlyWord
     *
     * @return string
     *
     * @see \Bitrix\Main\Grid\Declension
     */
    public static function getCardinalForRus(
        float $number,
        string $one,
        string $two,
        string $five,
        bool $onlyWord = false
    ): string {

        $word = (new \Bitrix\Main\Grid\Declension($one, $two, $five))->get($number);

        if ($onlyWord) {
            return $word;
        }

        return $number . ' ' . $word;
    }
}
