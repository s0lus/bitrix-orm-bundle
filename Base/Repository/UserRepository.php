<?php

namespace Prokl\BitrixOrmBundle\Base\Repository;

use Prokl\BitrixOrmBundle\Base\Collection\CdbResultItemCollection;
use Prokl\BitrixOrmBundle\Base\Exception\ItemNotFoundException;
use Prokl\BitrixOrmBundle\Base\Exception\UserNotAuthorizedException;
use Prokl\BitrixOrmBundle\Base\Factories\Interfaces\CdbResultItemFactoryInterface;
use Prokl\BitrixOrmBundle\Base\Factories\UserD7Factory;
use Prokl\BitrixOrmBundle\Base\Model\User;
use Prokl\BitrixOrmBundle\Base\Query\CdbResultQuery;
use Prokl\BitrixOrmBundle\Base\Query\UserQuery;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Entity\AddResult;
use Bitrix\Main\Entity\DeleteResult;
use Bitrix\Main\Entity\UpdateResult;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\UserTable;
use InvalidArgumentException;

class UserRepository extends CdbResultRepository
{
    /**
     * @var UserD7Repository Репозиторий работы с пользователями через D7. Нужен для случаев, когда работа через CUser
     *     не может быть выполнена.
     */
    private $userD7Repo;

    public function __construct(CdbResultItemFactoryInterface $factory)
    {
        parent::__construct($factory);
        $this->setUserD7Repo(
            new UserD7Repository(
                new UserTable,
                (new UserD7Factory($factory->getObjectWatcher()))
            )
        );
    }

    /**
     * @param User $user
     *
     * @return AddResult
     */
    public function add(User $user): AddResult
    {
        $addResult = $this->createQuery()
                          ->add($user);

        if ($addResult->isSuccess(true)) {
            $user->setId($addResult->getId());
            $this->getFactory()->getObjectWatcher()->removeItem($user);
        }

        return $addResult;
    }

    /**
     * @param User $user
     *
     * @return UpdateResult
     */
    public function update(User $user): UpdateResult
    {
        $updateResult = $this->createQuery()
                             ->update($user);

        if ($updateResult->isSuccess(true)) {
            $this->getFactory()->getObjectWatcher()->removeItem($user);
        }

        return $updateResult;
    }

    /**
     * @param User $user
     *
     * @return DeleteResult
     */
    public function delete(User $user): DeleteResult
    {
        return $this->deleteById($user->getId());
    }

    /**
     * @inheritDoc
     */
    public function findBy(
        array $criteria,
        array $order = ['timestamp_x' => 'desc'],
        int $limit = 0,
        int $offset = 0
    ): CdbResultItemCollection {

        return parent::findBy(
            $criteria,
            $order,
            $limit,
            $offset
        );
    }

    public function findActiveBy(
        array $criteria,
        array $order = ['timestamp_x' => 'desc'],
        int $limit = 0,
        int $offset = 0
    ): CdbResultItemCollection {
        return $this->findBy(
            array_merge($criteria, ['ACTIVE' => 'Y']),
            $order,
            $limit,
            $offset
        );
    }

    /**
     * @param integer $id
     *
     * @return User
     */
    public function findActiveById(int $id): User
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('Id must be positive integer number.');
        }

        $collection = $this->findActiveBy(['ID' => $id], [], 1);

        if ($collection->count() !== 1) {
            throw new ItemNotFoundException(
                sprintf(
                    'Active user having ID=%d not found.',
                    $id
                )
            );
        }

        return $collection->current();
    }

    /**
     * @param integer $id
     *
     * @return User
     */
    public function findById(int $id): User
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('Id must be positive integer number.');
        }

        $item = $this->getFactory()->getObjectWatcher()->get($this->getFactory()->getItemType(), $id);
        if ($item instanceof User) {
            return $item;
        }

        $collection = $this->findBy(['ID' => $id], [], 1);

        if ($collection->count() !== 1) {
            throw new ItemNotFoundException(
                sprintf(
                    'User having ID=%d not found.',
                    $id
                )
            );
        }

        return $collection->current();
    }

    /**
     * @param string $login
     *
     * @return User
     */
    public function findByLogin(string $login): User
    {
        $login = trim($login);
        if ('' === $login) {
            throw new InvalidArgumentException('Login must be non-empty string.');
        }

        $collection = $this->findBy(['LOGIN_EQUAL_EXACT' => $login], [], 1);

        if ($collection->count() !== 1) {
            throw new ItemNotFoundException(
                sprintf(
                    'User having login "%s" is not found',
                    $login
                )
            );
        }

        return $collection->current();
    }

    /**
     * @param string $login
     *
     * @return User
     */
    public function findActiveByLogin(string $login): User
    {
        $login = trim($login);
        if ('' === $login) {
            throw new InvalidArgumentException('Login must be non-empty string.');
        }

        $collection = $this->findActiveBy(['LOGIN_EQUAL_EXACT' => $login], [], 1);

        if ($collection->count() !== 1) {
            throw new ItemNotFoundException(
                sprintf(
                    'Active user having login "%s" is not found',
                    $login
                )
            );
        }

        return $collection->current();
    }

    /**
     * @param string $email
     *
     * @return User
     */
    public function findByEmail(string $email): User
    {
        $email = trim($email);
        if ('' === $email) {
            throw new InvalidArgumentException('Email must be non-empty valid string.');
        }

        $collection = $this->findBy(['=EMAIL' => $email], [], 1);

        if ($collection->count() !== 1) {
            throw new ItemNotFoundException(
                sprintf(
                    'User of type %s having EMAIL=`%s` not found',
                    $this->getFactory()->getItemType(),
                    $email
                )
            );
        }

        return $collection->current();
    }

    /**
     * @param string $email
     *
     * @return User
     */
    public function findActiveByEmail(string $email): User
    {
        $email = trim($email);
        if ('' === $email) {
            throw new InvalidArgumentException('Email must be non-empty valid string.');
        }

        $collection = $this->findActiveBy(['=EMAIL' => $email], [], 1);

        if ($collection->count() !== 1) {
            throw new ItemNotFoundException(
                sprintf(
                    'Active user of type %s having EMAIL=`%s` not found',
                    $this->getFactory()->getItemType(),
                    $email
                )
            );
        }

        return $collection->current();
    }

    /**
     * Возвращает пользователя по точному совпадению номера персонального телефона.
     *
     * Метод делает 2 SQL-запроса вместо одного, потому что поиск по точному соответствию через \CUser с параметром
     * фильтра 'PERSONAL_PHONE_EXACT_MATCH' => 'Y', невозможен, т.к. \CAllFilterQuery::ParseStr() будет всегда отрезать
     * знак "+" у телефонного номера, представленного в международном формате E.164(например, '+79009990002').
     *
     * @param string $phone
     *
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @return User
     */
    public function findByPersonalPhone(string $phone): User
    {
        return $this->findById(
            $this->getUserD7Repo()
                 ->findByPersonalPhone($phone)
                 ->getId()
        );
    }

    /**
     * @throws UserNotAuthorizedException
     * @return User
     */
    public function findCurrentUser(): User
    {
        if (!$this->createQuery()->getCUser()->IsAuthorized()) {
            throw new UserNotAuthorizedException(
                'User is not authorized.'
            );
        }

        return $this->findById((int)$this->createQuery()->getCUser()->GetID());
    }

    /**
     * @return UserQuery
     */
    protected function createQuery(): CdbResultQuery
    {
        return new UserQuery();
    }

    /**
     * @return UserD7Repository
     */
    public function getUserD7Repo(): UserD7Repository
    {
        return $this->userD7Repo;
    }

    /**
     * @param UserD7Repository $userD7Repo
     *
     * @return $this
     */
    public function setUserD7Repo(UserD7Repository $userD7Repo)
    {
        $this->userD7Repo = $userD7Repo;

        return $this;
    }
}
