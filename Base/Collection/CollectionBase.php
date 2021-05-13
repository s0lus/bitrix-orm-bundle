<?php

namespace Prokl\BitrixOrmBundle\Base\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\HasNameInterface;

/**
 * Class CollectionBase
 * @package Prokl\BitrixOrmBundle\Base\Collection
 */
abstract class CollectionBase extends ArrayCollection
{
    /**
     * Возвращает названия элементов в виде строки.
     *
     * @param string $glue
     *
     * @return string
     */
    public function getImplodedNames(string $glue = ', '): string
    {
        $doGetName = static function (HasNameInterface $item) {
            return $item->getName();
        };

        return implode(
            $glue,
            array_map($doGetName, $this->toArray())
        );
    }
}
