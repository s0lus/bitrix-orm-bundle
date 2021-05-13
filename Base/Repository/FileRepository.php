<?php

namespace Prokl\BitrixOrmBundle\Base\Repository;

use Prokl\BitrixOrmBundle\Base\Collection\CdbResultItemCollection;
use Prokl\BitrixOrmBundle\Base\Exception\ItemNotFoundException;
use Prokl\BitrixOrmBundle\Base\Factories\FileFactory;
use Prokl\BitrixOrmBundle\Base\Factories\Interfaces\CdbResultItemFactoryInterface;
use Prokl\BitrixOrmBundle\Base\Model\File;
use Prokl\BitrixOrmBundle\Base\Query\CdbResultQuery;
use Prokl\BitrixOrmBundle\Base\Query\FileQuery;
use InvalidArgumentException;

class FileRepository extends CdbResultRepository
{
    public function __construct(FileFactory $factory)
    {
        parent::__construct($factory);
    }

    /**
     * @return FileFactory
     */
    public function getFactory(): CdbResultItemFactoryInterface
    {
        return parent::getFactory();
    }

    /**
     * @inheritDoc
     *
     * @param integer $limit Параметр не поддерживается из-за ограничений \CAllFile::GetList()
     * @param integer $offset Параметр не поддерживается из-за ограничений \CAllFile::GetList()
     */
    public function findBy(array $criteria, array $order = [], int $limit = 0, int $offset = 0): CdbResultItemCollection
    {
        return parent::findBy($criteria, $order, $limit, $offset);
    }

    /**
     * @param integer $id
     *
     * @return File
     *
     * @deprecated Заменён на findById() для единообразия с другими репозиториями.
     * @see FileRepository::findById()
     */
    public function getById(int $id): File
    {
        return $this->findById($id);
    }

    /**
     * @param integer $id
     *
     * @throws ItemNotFoundException
     *
     * @return File
     * @since 3.1.2
     */
    public function findById(int $id): File
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('Id must be positive number.');
        }

        $existingFile = $this->getFactory()->getObjectWatcher()->get($this->getFactory()->getItemType(), $id);
        if ($existingFile instanceof File) {
            return $existingFile;
        }

        $collection = $this->findBy(['ID' => $id]);

        if ($collection->count() !== 1) {
            throw new ItemNotFoundException(
                sprintf(
                    'File item of type %s having ID=%d not found.',
                    $this->getFactory()->getItemType(),
                    $id
                )
            );
        }

        return $collection->current();
    }

    /**
     * @inheritDoc
     * @param integer $limit Параметр не поддерживается из-за ограничений \CAllFile::GetList()
     * @param integer $offset Параметр не поддерживается из-за ограничений \CAllFile::GetList()
     */
    public function findByIdList(
        array $idList,
        array $criteria = [],
        array $order = [],
        int $limit = 0,
        int $offset = 0
    ): CdbResultItemCollection {
        if (count($idList) === 0) {
            return $this->getFactory()->createCollectionFromArray([]);
        }

        return $this->findBy(
            array_merge($criteria, ['@ID' => $idList]),
            $order,
            $limit,
            $offset
        );
    }

    /**
     * Возвращает файл по идентификатору. Если файл не найден, вернёт объект файла с пустыми параметрами.
     *
     * @param integer $id
     *
     * @return File
     * @see \Prokl\BitrixOrmBundle\Base\Repository\FileRepository::findById()
     */
    public function getFileById(int $id): File
    {
        if ($id <= 0) {
            return $this->getFactory()->createItem([]);
        }

        try {
            return $this->findById($id);
        } catch (ItemNotFoundException $exception) {
            return $this->getFactory()->createItem([]);
        }
    }

    /**
     * @inheritDoc
     * @return FileQuery
     */
    protected function createQuery(): CdbResultQuery
    {
        return new FileQuery();
    }
}
