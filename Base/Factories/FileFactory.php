<?php

namespace Prokl\BitrixOrmBundle\Base\Factories;


use Prokl\BitrixOrmBundle\Base\Model\File;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\BitrixArrayItemInterface;

class FileFactory extends CdbResultItemFactory
{
    /**
     * @inheritdoc
     */
    public function getSelect(): array
    {
        return [];
    }

    /**
     * @param array $data
     *
     * @return File
     */
    public function createItem(array $data): BitrixArrayItemInterface
    {
        return File::createFromArray($data);
    }

}
