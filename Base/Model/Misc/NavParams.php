<?php

namespace Prokl\BitrixOrmBundle\Base\Model\Misc;

class NavParams
{
    /**
     * ограничить количество сверху
     */
    const TOP_COUNT = 'nTopCount';

    /**
     * ID элемента который будет выбран вместе со своими соседями. Количество соседей определяется параметром
     * nPageSize. Например: если nPageSize равно 2-м, то будут выбраны максимум 5-ть элементов.  Соседи определяются
     * порядком сортировки заданным в параметре arOrder
     */
    const ELEMENT_ID = 'nElementID';

    /**
     * количество элементов на странице при постраничной навигации
     */
    const PAGE_SIZE = 'nPageSize';

    /**
     * разрешить вывести все элементы при постраничной навигации
     */
    const SHOW_ALL = 'bShowAll';

    /**
     * номер страницы при постраничной навигации
     */
    const NUM_PAGE = 'iNumPage';

    /**
     * @var int количество элементов на странице при постраничной навигации
     */
    protected $nPageSize = null;

    /**
     * @var int ограничить количество сверху
     */
    protected $nTopCount = null;

    /**
     * @var int номер страницы при постраничной навигации
     */
    protected $iNumPage = null;

    /**
     * @var bool разрешить вывести все элементы при постраничной навигации
     */
    protected $bShowAll = null;

    /**
     * @var bool Возвращать пустой результат, если выбран номер страницы больше, чем доступно.
     */
    protected $checkOutOfRange = true;

    /**
     * @var int ID элемента который будет выбран вместе со своими соседями. Количество соседей определяется параметром
     * nPageSize. Например: если nPageSize равно 2-м, то будут выбраны максимум 5-ть элементов.  Соседи определяются
     * порядком сортировки заданным в параметре arOrder
     */
    protected $nElementId = null;

    public function __construct(array $fields = [])
    {
        if (array_key_exists(self::PAGE_SIZE, $fields)) {

            $this->setPageSize((int)$fields[self::PAGE_SIZE]);

        } elseif (array_key_exists(self::TOP_COUNT, $fields)) {

            $this->setTopCount((int)$fields[self::TOP_COUNT]);

        } elseif (array_key_exists(self::NUM_PAGE, $fields)) {

            $this->setNumPage((int)$fields[self::NUM_PAGE]);

        } elseif (array_key_exists(self::SHOW_ALL, $fields)) {

            $this->setShowAll((bool)$fields[self::SHOW_ALL]);

        } elseif (array_key_exists(self::ELEMENT_ID, $fields)) {

            $this->setElementId((int)$fields[self::ELEMENT_ID]);

        }
    }

    /**
     * @param array $fields
     *
     * @return NavParams
     */
    public static function createFromArray(array $fields = []): NavParams
    {
        return new static($fields);
    }

    /**
     * Возвращает массив 'arNavParams' в пригодном для Битрикса виде. В том числе, возвращается (bool)false, если
     * параметры постраничной навигации не заданы.
     *
     * @return array|bool
     */
    public function toArray()
    {
        $asArray = [];

        foreach (get_object_vars($this) as $field => $value) {

            if (is_null($value)) {
                continue;
            }

            $asArray[$field] = $value;
        }

        /**
         * Защита от возвращения пустого массива,
         * который при передаче в *::GetList вызовет ограничение
         * на выборку 10 элементов (или разделов) инфоблока.
         *
         * А также защита от передачи массива с единственным 'checkOutOfRange' => true,
         * что ведёт к возвращению только первого элемента выборки.
         * (Да и вообще, передача любого непустого массива,
         * который не задаёт корректные параметры постраничной навигации,
         * будет приводить к возвращению первого элемента выборки)
         */
        if (
            count($asArray) === 0
            || (
                count($asArray) === 1
                && array_key_exists('checkOutOfRange', $asArray)
            )
        ) {
            return false;
        }

        return $asArray;
    }

    /**
     * @return integer
     */
    public function getPageSize(): int
    {
        return (int)$this->nPageSize;
    }

    /**
     * @param integer $nPageSize
     *
     * @return $this
     */
    public function setPageSize(int $nPageSize)
    {
        $this->nPageSize = $nPageSize;

        return $this;
    }

    /**
     * @return integer
     */
    public function getTopCount(): int
    {
        return (int)$this->nTopCount;
    }

    /**
     * @param integer $nTopCount
     *
     * @return $this
     */
    public function setTopCount(int $nTopCount)
    {
        $this->nTopCount = $nTopCount;

        return $this;
    }

    /**
     * @return integer
     */
    public function getNumPage(): int
    {
        return (int)$this->iNumPage;
    }

    /**
     * @param integer $iNumPage
     *
     * @return $this
     */
    public function setNumPage(int $iNumPage)
    {
        $this->iNumPage = $iNumPage;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isShowAll(): bool
    {
        return (bool)$this->bShowAll;
    }

    /**
     * @param boolean $bShowAll
     *
     * @return $this
     */
    public function setShowAll(bool $bShowAll)
    {
        $this->bShowAll = $bShowAll;

        return $this;
    }

    /**
     * @return integer
     */
    public function getElementId(): int
    {
        return (int)$this->nElementId;
    }

    /**
     * @param integer $nElementId
     *
     * @return $this
     */
    public function setElementId(int $nElementId)
    {
        $this->nElementId = $nElementId;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isCheckOutOfRange(): bool
    {
        return $this->checkOutOfRange;
    }

    /**
     * @param boolean $checkOutOfRange
     *
     * @return $this
     */
    public function setCheckOutOfRange(bool $checkOutOfRange)
    {
        $this->checkOutOfRange = $checkOutOfRange;

        return $this;
    }
}
