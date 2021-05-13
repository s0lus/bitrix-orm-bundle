<?php

namespace Prokl\BitrixOrmBundle\Base\Model\Interfaces;

use Prokl\BitrixOrmBundle\Base\Repository\FileRepository;

interface FileRepositoryAwareInterface
{
    /**
     * @return FileRepository
     */
    public static function getFileRepository(): FileRepository;

    /**
     * @param FileRepository $fileRepository
     */
    public static function setFileRepository(FileRepository $fileRepository);
}
