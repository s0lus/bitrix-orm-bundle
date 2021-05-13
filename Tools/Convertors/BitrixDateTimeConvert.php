<?php

namespace Prokl\BitrixOrmBundle\Tools\Convertors;

use Bitrix\Main\Type\DateTime;
use DateTimeImmutable;
use DateTimeZone;

/**
 * Class BitrixDateTimeConvert
 * @package Prokl\BitrixOrmBundle\Tools\Convertors
 */
class BitrixDateTimeConvert
{
    /**
     * Конвертирует строку с датой и временем в формате сайта в объект DateTimeImmutable
     *
     * @param string $dateTime
     * @param string|bool $fromSite Идентификатор сайта, в формате которого было задано время time.
     *      Необязательный параметр. По умолчанию - текущий сайт.
     * @param boolean $searchInSitesOnly Необязательный параметр. По умолчанию - "false", текущий сайт.
     * @param DateTimeZone|null $timeZone
     *
     * @return bool|DateTimeImmutable false при ошибке.
     *
     * @link https://dev.1c-bitrix.ru/api_help/main/functions/date/convertdatetime.php
     */
    public static function bitrixStringDateTimeToDateTimeImmutable(
        string $dateTime,
        $fromSite = false,
        bool $searchInSitesOnly = false,
        DateTimeZone $timeZone = null
    ) {
        return DateTimeImmutable::createFromFormat(
            'Y-m-d\TH:i:s',
            sprintf(
                '%sT%s',
                ConvertDateTime($dateTime, 'YYYY-MM-DD', $fromSite, $searchInSitesOnly),
                ConvertDateTime($dateTime, 'HH:MI:SS', $fromSite, $searchInSitesOnly)
            ),
            $timeZone
        );
    }

    /**
     * Конвертирует объект DateTimeImmutable в строку с датой и временем в формате сайта.
     *
     * @param DateTimeImmutable $dateTimeImmutable
     * @param string $type Тип формата. Допустимы следующие значения:
     *      <ul>
     *          <li>FULL - полный (дата и время)</li>
     *          <li>SHORT - короткий (дата)</li>
     *      </ul>
     * @param boolean $site Идентификатор сайта, в формате которого необходимо вернуть дату.
     *      Необязательный параметр. По умолчанию - текущий сайт.
     * @param boolean $searchInSitesOnly Искать только на сайте.
     *      Необязательный параметр. По умолчанию - "false" текущий сайт.
     *
     * @return string
     *
     * @link https://dev.1c-bitrix.ru/api_help/main/functions/date/converttimestamp.php
     */
    public static function dateTimeImmutableToBitrixStringDateTime(
        DateTimeImmutable $dateTimeImmutable,
        $type = 'SHORT',
        $site = false,
        bool $searchInSitesOnly = false
    ): string {

        return ConvertTimeStamp(
            $dateTimeImmutable->getTimestamp(),
            $type,
            $site,
            $searchInSitesOnly
        );
    }

    /**
     * Конвертирует объект DateTimeImmutable в Битриксовый объект DateTime
     *
     * @param DateTimeImmutable $dateTimeImmutable
     *
     * @return DateTime
     */
    public static function dateTimeImmutableToBitrixDateTime(DateTimeImmutable $dateTimeImmutable): DateTime
    {
        return DateTime::createFromPhp(
            \DateTime::createFromFormat(
                DATE_ISO8601,
                $dateTimeImmutable->format(DATE_ISO8601),
                /**
                 * Позволяет сохранить временную зону в точности так, как она была у исходной даты.
                 */
                $dateTimeImmutable->getTimezone()
            )
        );
    }
}
