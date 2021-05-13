<?php

namespace Prokl\BitrixOrmBundle\Base\Factories;

use Prokl\BitrixOrmBundle\Base\Model\IblockElement;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\BitrixArrayItemInterface;
use Prokl\BitrixOrmBundle\Base\ObjectWatcher;
use Prokl\BitrixOrmBundle\Base\Repository\FileRepository;

/**
 * Class IblockElementFactory
 * @package Prokl\BitrixOrmBundle\Base\Factories
 */
abstract class IblockElementFactory extends CdbResultItemFactory
{
    /**
     * @var FileRepository
     */
    protected $fileRepository;

    /**
     * IblockElementFactory constructor.
     *
     * @param ObjectWatcher  $objectWatcher
     * @param FileRepository $fileRepository
     */
    public function __construct(ObjectWatcher $objectWatcher, FileRepository $fileRepository)
    {
        parent::__construct($objectWatcher);
        $this->fileRepository = $fileRepository;
    }

    /**
     * @param array $data
     *
     * @return IblockElement
     */
    abstract public function createItem(array $data): BitrixArrayItemInterface;
}
