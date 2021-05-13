<?php

namespace Prokl\BitrixOrmBundle\Base\Query;

use Prokl\BitrixOrmBundle\Base\Model\Misc\NavParams;
use Bitrix\Main\Entity\DeleteResult;
use CDBResult;
use InvalidArgumentException;

/**
 * Class CdbResultQuery
 * @package Prokl\BitrixOrmBundle\Base\Query
 */
abstract class CdbResultQuery extends QueryBase
{
    /**
     * @var NavParams
     */
    protected $navParams;

    /**
     * Непосредственное выполнение запроса через API Битрикса
     *
     * @return bool|CDBResult|int
     */
    abstract public function exec();

    /**
     * @return NavParams
     */
    public function getNavParams(): NavParams
    {
        if (is_null($this->navParams)) {
            $this->navParams = NavParams::createFromArray([]);
        }

        return $this->navParams;
    }

    /**
     * @return array|boolean
     * @deprecated
     */
    public function getNavParamsAsArray()
    {
        return $this->getNavParams()->toArray();
    }

    /**
     * @param NavParams $navParams
     *
     * @return $this
     */
    public function setNavParams(NavParams $navParams): self
    {
        $this->navParams = $navParams;
        $this->initLimitAndOffsetByNav();

        return $this;
    }

    /**
     * @param integer $offset
     *
     * @return $this
     */
    public function setOffset(int $offset)
    {
        parent::setOffset($offset);

        $this->initNavByLimitAndOffset();

        return $this;
    }

    /**
     * @param integer $limit
     *
     * @return $this
     */
    public function setLimit(int $limit)
    {
        parent::setLimit($limit);

        $this->initNavByLimitAndOffset();

        return $this;
    }

    /**
     * Инициализирует limit и offset по массиву navParams с параметрами постраничной навигации
     */
    private function initLimitAndOffsetByNav()
    {
        /**
         * Конвертация в соответствующие limit и offset
         */
        $this->setLimit(0);
        $navParams = $this->getNavParams();
        if ($navParams->getTopCount() > 0) {
            $this->setLimit($navParams->getTopCount());
        } elseif ($navParams->getPageSize() > 0) {
            $this->setLimit($navParams->getPageSize());
        }

        /**
         * Если задан номер страницы, но не известен размер страницы,
         * то ни к какому смещению это не приводит,
         * т.к. его нельзя расчитать и это ошибка.
         */
        if ($navParams->getNumPage() > 0 && $navParams->getPageSize() <= 0) {
            throw new InvalidArgumentException(
                sprintf(
                    'Nav must have `%s` specified if `%s` is specified.',
                    NavParams::PAGE_SIZE,
                    NavParams::NUM_PAGE
                )
            );
        }

        $this->setOffset(0);
        if ($navParams->getNumPage() > 0 && $this->navParams->getPageSize() > 0) {
            $this->setOffset(
                (int)(($navParams->getNumPage() - 1) * $this->navParams->getPageSize())
            );
        }
    }

    /**
     * Инициализирует navParams с параметрами постраничной навигации по limit и offset
     */
    private function initNavByLimitAndOffset()
    {
        /**
         * Если не задан лимит и задано смещение,
         * или если задано смещение и лимит и это не укладывается в постраничную навигацию,
         * то это ошибка
         */
        if (
            ($this->getLimit() === 0 && $this->getOffset() > 0)
            || ($this->getLimit() > 0 && $this->getOffset() > 0 && $this->getOffset() % $this->getLimit() !== 0)
        ) {
            /*
             * TODO Предложение по улушчению: можно всё равно расчитать такой лимит и номер страницы,
             *  чтобы выбрать больше данных, а затем пересоздать CdbResult по массиву, где отрезать то,
             *  что не подходит под требуемый offset и limit.
             */
            throw new InvalidArgumentException(
                sprintf(
                    'Impossible to init navParams: limit %d and offset %d could not be converted to integer %s. Use setNavParams()',
                    $this->getLimit(),
                    $this->getOffset(),
                    NavParams::NUM_PAGE
                )
            );
        }

        if ($this->getLimit() > 0) {
            $this->getNavParams()
                 ->setPageSize($this->getLimit())
                 ->setNumPage(1 + ($this->getOffset() / $this->getLimit()));
        }
    }

    /**
     * @param integer $id
     *
     * @return DeleteResult
     */
    abstract public function delete(int $id): DeleteResult;
}
