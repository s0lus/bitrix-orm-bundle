<?php

namespace Prokl\BitrixOrmBundle\Base\Repository;

use Prokl\BitrixOrmBundle\Base\Collection\CdbResultItemCollection;
use Prokl\BitrixOrmBundle\Base\Factories\Interfaces\CdbResultItemFactoryInterface;
use Prokl\BitrixOrmBundle\Base\Model\SearchItem;
use Prokl\BitrixOrmBundle\Base\Query\CdbResultQuery;
use Prokl\BitrixOrmBundle\Base\Query\SearchQuery;

class SearchRepository extends CdbResultRepository
{
     /**
     * @var boolean
     */
    private $searchNoMorphWhenEmpty;

     /**
     * @var boolean
     */
    private $errorOnEmptyStem;

    /**
     * SearchRepository constructor.
     *
     * @param CdbResultItemFactoryInterface $factory
     * @param boolean $searchNoMorphWhenEmpty Искать без учёта морфологии при отсутствии результатов поиска.
     * @param boolean $errorOnEmptyStem Возвращать ошибку при пустом поисковом запросе.
     */
    public function __construct(
        CdbResultItemFactoryInterface $factory,
        bool $searchNoMorphWhenEmpty = true,
        bool $errorOnEmptyStem = true
    ) {
        parent::__construct($factory);
        $this->searchNoMorphWhenEmpty = $searchNoMorphWhenEmpty;
        $this->errorOnEmptyStem = $errorOnEmptyStem;
    }

    /**
     * Возвращает результат выполнения поискового запроса.
     *
     * @param array $criteria Массив, содержащий условия поиска.
     * @param array $order Массив, содержащий признак сортировки
     * @param integer $limit
     * @param integer $offset
     * @param array $criteriaEx Массив массивов, содержащий дополнительные условия поиска.
     *
     * @return CdbResultItemCollection|SearchItem[]
     *
     * @link https://dev.1c-bitrix.ru/api_help/search/classes/csearch/search.php
     */
    public function findBy(
        array $criteria,
        array $order = ["CUSTOM_RANK" => "DESC", "RANK" => "DESC", "DATE_CHANGE" => "DESC"],
        int $limit = 0,
        int $offset = 0,
        array $criteriaEx = []
    ): CdbResultItemCollection {
        $result = $this->createQuery()
                       ->setSearchNoMorphWhenEmpty($this->searchNoMorphWhenEmpty)
                       ->setErrorOnEmptyStem($this->errorOnEmptyStem)
                       ->setFilter($criteria)
                       ->setFilterEx($criteriaEx)
                       ->setOrder($order)
                       ->setLimit($limit)
                       ->setOffset($offset)
                       ->exec();

        return $this->getFactory()->createCollection($result);
    }

    /**
     * @return SearchQuery
     */
    protected function createQuery(): CdbResultQuery
    {
        return new SearchQuery();
    }
}
