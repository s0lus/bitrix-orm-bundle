<?php

namespace Prokl\BitrixOrmBundle\Base\Model\Interfaces;

/**
 * Interface BitrixArrayItemInterface
 *
 * Работа с массивами как с объектами - главная идея пакета BitrixORM. Из массива можно создать объект и удобно
 * работать с ним в коде. И также можно обратно получить массив из объекта и отправить его в API Битрикс, если он не
 * умеет толком работать ни с чем, кроме ассоциативных массивов.
 *
 * @package Prokl\BitrixOrmBundle\Base\\Model\Interfaces
 */
interface BitrixArrayItemInterface extends HasIdInterface
{
    /**
     * Возвращает массив, представляющий объект в понятном API Битрикса виде
     *
     * @return array
     */
    public function toArray(): array;

    /**
     * Возвращает объект сущности, созданной из массива в формате, получаемом от API Битрикса.
     *
     * @param array $fields
     *
     * @return BitrixArrayItemInterface
     */
    public static function createFromArray(array $fields = []): BitrixArrayItemInterface;
}
