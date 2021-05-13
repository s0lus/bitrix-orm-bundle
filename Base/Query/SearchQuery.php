<?php

namespace Prokl\BitrixOrmBundle\Base\Query;

use Bitrix\Main\Entity\DeleteResult;
use CDBResult;
use CSearch;
use LogicException;

class SearchQuery extends CdbResultQuery
{
     /**
     * @var boolean
     */
    private $searchNoMorphWhenEmpty = false;

     /**
     * @var boolean
     */
    private $errorOnEmptyStem = true;

    /**
     * @var array
     */
    protected $filterEx = [];

    /**
     * @return bool|CDBResult|CSearch|int
     */
    public function exec()
    {
        $result = $this->doExec();

        /**
         * Поиск с выключенной морфологией,
         * если нет результатов.
         */
        if (
            $this->isSearchNoMorphWhenEmpty()
            && $result->SelectedRowsCount() === 0
        ) {
            $paramsEx = $this->getFilterEx();
            $paramsEx['STEMMING'] = false;

            $result = $this->doExec($paramsEx);

        }

        return $result;
    }

    /**
     * @param array|null $filterEx
     *
     * @return CSearch
     */
    protected function doExec(array $filterEx = null): CSearch
    {
        $result = $this->getCSearch();
        $result->Search(
            $this->getFilter(),
            $this->getOrder(),
            is_array($filterEx) ? $filterEx : $this->getFilterEx()
        );

        $result->NavStart(
            $this->getLimit(),
            false,
            $this->getNavParams()->getNumPage()
        );

        return $result;
    }

    /**
     * @return boolean
     */
    public function isSearchNoMorphWhenEmpty(): bool
    {
        return $this->searchNoMorphWhenEmpty;
    }

    /**
     * Искать без учёта морфологии при отсутствии результатов поиска.
     *
     * @param boolean $searchNoMorphWhenEmpty
     *
     * @return $this
     */
    public function setSearchNoMorphWhenEmpty(bool $searchNoMorphWhenEmpty)
    {
        $this->searchNoMorphWhenEmpty = $searchNoMorphWhenEmpty;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isErrorOnEmptyStem(): bool
    {
        return $this->errorOnEmptyStem;
    }

    /**
     * Возвращать ошибку при пустом поисковом запросе.
     *
     * @param boolean $errorOnEmptyStem
     *
     * @return $this
     */
    public function setErrorOnEmptyStem(bool $errorOnEmptyStem)
    {
        $this->errorOnEmptyStem = $errorOnEmptyStem;

        return $this;
    }

    /**
     * @return CSearch
     */
    protected function getCSearch(): CSearch
    {
        $search = new CSearch();

        $search->SetOptions(['ERROR_ON_EMPTY_STEM' => $this->isErrorOnEmptyStem()]);

        return $search;
    }

    /**
     * @return array
     */
    public function getFilterEx(): array
    {
        return $this->filterEx;
    }

    /**
     * @param array $filterEx
     *
     * @return $this
     */
    public function setFilterEx(array $filterEx)
    {
        $this->filterEx = $filterEx;

        return $this;
    }

    /**
     * Не поддерживается.
     *
     * @return array
     */
    public function getSelect(): array
    {
        $this->throwNotSupported(__METHOD__);

        return [];
    }

    /**
     * Не поддерживается.
     *
     * @param array $select
     *
     * @return CdbResultQuery|void
     */
    public function setSelect(array $select)
    {
        $this->throwNotSupported(__METHOD__);
    }

    /**
     * Не поддерживается.
     *
     * @return array
     */
    public function getGroup(): array
    {
        $this->throwNotSupported(__METHOD__);

        return [];
    }

    /**
     * Не поддерживается.
     *
     * @param array $group
     *
     * @return CdbResultQuery|void
     */
    public function setGroup(array $group)
    {
        $this->throwNotSupported(__METHOD__);
    }

    /**
     * @param string $methodName
     */
    private function throwNotSupported(string $methodName)
    {
        throw new LogicException(
            sprintf(
                'Method %s is not supported',
                $methodName
            )
        );
    }

    /**
     * @inheritDoc
     * @return void
     */
    public function delete(int $id): DeleteResult
    {
        $this->throwNotSupported(__METHOD__);
    }
}
