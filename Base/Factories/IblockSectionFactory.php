<?php

namespace Prokl\BitrixOrmBundle\Base\Factories;

use Prokl\BitrixOrmBundle\Base\Model\IblockSection;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\BitrixArrayItemInterface;
use Prokl\BitrixOrmBundle\Base\ObjectWatcher;
use Prokl\BitrixOrmBundle\Base\Repository\FileRepository;

abstract class IblockSectionFactory extends CdbResultItemFactory
{
    /**
     * @var FileRepository
     */
    protected $fileRepository;

    public function __construct(ObjectWatcher $objectWatcher, FileRepository $fileRepository)
    {
        parent::__construct($objectWatcher);
        $this->fileRepository = $fileRepository;
    }

    /**
     * @param array $data
     *
     * @return IblockSection
     */
    abstract public function createItem(array $data): BitrixArrayItemInterface;

}
