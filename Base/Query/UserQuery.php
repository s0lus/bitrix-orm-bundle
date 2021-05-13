<?php

namespace Prokl\BitrixOrmBundle\Base\Query;

use Prokl\BitrixOrmBundle\Base\Model\User;
use Bitrix\Main\Entity\AddResult;
use Bitrix\Main\Entity\DeleteResult;
use Bitrix\Main\Entity\UpdateResult;
use Bitrix\Main\Error;
use CApplicationException;
use CUser;

class UserQuery extends CdbResultQuery
{
    /**
     * Выбирает все поля, в том числе и пользовательские.
     */
    const SELECT_ALL = ['*', 'UF_*'];

    /**
     * @var CUser
     */
    private static $CUser;

    public function __construct()
    {
        $this->setSelect(self::SELECT_ALL);
    }

    /**
     * @inheritDoc
     * @link https://dev.1c-bitrix.ru/api_help/main/reference/cuser/getlist.php
     */
    public function exec()
    {
        /**
         * Согласно документации, начиная с версии ядра 11.0.13 ,
         * первый аргумент - массив сортировок, а второй игнорируется.
         * @link https://dev.1c-bitrix.ru/api_help/main/reference/cuser/getlist.php
         */
        $uselessVariable = '';
        $order = $this->getOrder();

        return CUser::GetList(
            $order,
            $uselessVariable,
            $this->getFilter(),
            $this->getParameters()
        );
    }

    /**
     * Возвращает параметры для CUser::GetList, содержащие два вида настроек выборки полей и настройки навигации.
     *
     * @return array
     */
    private function getParameters(): array
    {
        $parameters = [];

        $navArray = $this->getNavParams()->toArray();
        if (is_array($navArray)) {
            $parameters['NAV_PARAMS'] = $navArray;
        }

        /**
         * Разделение select на пользовательские поля и обычные поля.
         */
        $selectParamList = [];
        $fieldParamList = [];
        foreach ($this->getSelect() as $value) {
            if (substr($value, 0, 3) === 'UF_') {
                $selectParamList[] = $value;
            } else {
                $fieldParamList[] = $value;
            }
        }

        if (count($selectParamList) > 0) {
            $parameters['SELECT'] = $selectParamList;
        }

        if (count($fieldParamList) > 0) {
            $parameters['FIELDS'] = $fieldParamList;
        }

        return $parameters;
    }

    /**
     * @param User $user
     *
     * @return AddResult
     */
    public function add(User $user): AddResult
    {
        $addResult = new AddResult();

        $id = $this->getCUser()->Add($user->toArray());
        if ($id > 0) {
            $user->setId($id);
            $addResult->setId($id);
        } else {
            $addResult->addError(new Error($this->getCUser()->LAST_ERROR));
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
        $updateResult = new UpdateResult();

        $update = $this->getCUser()->Update($user->getId(), $user->toArray());
        if ($update) {
            $updateResult->setPrimary([$user->getId()]);
        } else {
            $updateResult->addError(new Error($this->getCUser()->LAST_ERROR));
        }

        return $updateResult;
    }

    /**
     * @param integer $id
     *
     * @return DeleteResult
     */
    public function delete(int $id): DeleteResult
    {
        global $APPLICATION;

        $deleteResult = new DeleteResult();

        if (!$this->getCUser()::Delete($id)) {
            $errorCode = 0;
            $errorMessage = 'unknown error';
            $applicationException = $APPLICATION->GetException();

            if ($applicationException instanceof CApplicationException) {
                $errorMessage = $applicationException->GetString();
                $errorCode = $applicationException->GetID();
            }
            $deleteResult->addError(new Error($errorMessage, $errorCode));
        }

        return $deleteResult;
    }

    /**
     * @return CUser
     */
    public function getCUser(): CUser
    {
        if (is_null(self::$CUser)) {
            self::$CUser = new CUser();
        }

        return self::$CUser;
    }
}
