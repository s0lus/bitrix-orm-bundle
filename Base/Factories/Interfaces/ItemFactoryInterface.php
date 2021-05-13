<?php

namespace Prokl\BitrixOrmBundle\Base\Factories\Interfaces;

use Prokl\BitrixOrmBundle\Base\ObjectWatcher;

interface ItemFactoryInterface
{
    /**
     * Возвращает набор полей и свойств, которые следует передать в select, чтобы получить информацию, достаточную для
     * создания объекта сущности.
     *
     * @return array
     */
    public function getSelect(): array;

    /**
     * Возвращает тип объекта, который создаёт данная фабрика.
     *
     * @return string
     */
    public function getItemType(): string;

    /**
     * @return ObjectWatcher
     */
    public function getObjectWatcher(): ObjectWatcher;
}
