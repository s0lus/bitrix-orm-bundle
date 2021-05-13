<?php

namespace Prokl\BitrixOrmBundle\Base\Model\Traits;

use Prokl\BitrixOrmBundle\Base\Repository\FileRepository;

trait FileRepositoryAwareTrait
{
    /**
     * @var FileRepository Статическое свойство чтобы не мешать процессу сериализации/десериализации модельного объекта.
     */
    protected static $fileRepository;

    /**
     * @return FileRepository
     */
    public static function getFileRepository(): FileRepository
    {
        return self::$fileRepository;
    }

    /**
     * @param FileRepository $fileRepository
     */
    public static function setFileRepository(FileRepository $fileRepository)
    {
        self::$fileRepository = $fileRepository;
    }

}
