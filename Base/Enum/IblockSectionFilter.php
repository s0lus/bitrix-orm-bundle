<?php

namespace Prokl\BitrixOrmBundle\Base\Enum;

class IblockSectionFilter
{
    /**
     * Подсчитывать элементы вложенных подразделов или нет (Y|N). По умолчанию Y
     */
    const ELEMENT_SUBSECTIONS = 'ELEMENT_SUBSECTIONS';

    /**
     * Подсчитывать еще неопубликованные элементы (Y|N). По умолчанию N. Актуально при установленном модуле
     * документооборота
     */
    const CNT_ALL = 'CNT_ALL';

    /**
     * При подсчете учитывать активность элементов (Y|N). По умолчанию N. Учитывается флаг активности элемента ACTIVE и
     * даты начала и окончания активности.
     */
    const CNT_ACTIVE = 'CNT_ACTIVE';
}
