<?php

namespace Prokl\BitrixOrmBundle\Base\Model;

use Prokl\BitrixOrmBundle\Base\Model\Interfaces\HasActiveInterface;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\HasNameInterface;
use Prokl\BitrixOrmBundle\Base\Model\Misc\UserGroupInvolvement;
use Prokl\BitrixOrmBundle\Base\Model\Traits\HasActiveAsStringTrait;
use Prokl\BitrixOrmBundle\Base\Model\Traits\HasNameTrait;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use InvalidArgumentException;
use Prokl\BitrixOrmBundle\Tools\Convertors\BitrixDateTimeConvert;

/**
 * Class User
 * @package Prokl\BitrixOrmBundle\Base\Model
 */
class User extends D7Item implements HasNameInterface, HasActiveInterface
{
    use HasNameTrait;
    use HasActiveAsStringTrait;

    public const CHECK_WORD_TIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * @var array
     */
    protected $GROUP_ID;

    /**
     * @var string
     */
    protected $LAST_NAME;

    /**
     * @var string
     */
    protected $SECOND_NAME;

    /**
     * @var string
     */
    protected $EMAIL;

    /**
     * @var string
     */
    protected $LOGIN;

    /**
     * @var string
     */
    protected $PASSWORD;

    /**
     * @var string
     */
    protected $PASSWORD_CONFIRM;

    /**
     * @var integer
     */
    protected $LOGIN_ATTEMPTS;

    /**
     * @var string
     */
    protected $CHECKWORD;

    /**
     * @var string
     */
    protected $PERSONAL_PHONE;

    /**
     * @var null|string
     */
    protected $PERSONAL_GENDER;

    /**
     * @var null|string
     */
    protected $PERSONAL_BIRTHDAY;

    /**
     * @var null|DateTimeImmutable
     */
    protected $birthday;

    /**
     * @var string
     */
    protected $WORK_PHONE;

    /**
     * @var string
     */
    protected $WORK_POSITION;

    /**
     * @var string
     */
    protected $WORK_COMPANY;

    /**
     * @var string
     */
    protected $ADMIN_NOTES;

    /**
     * @var DateTimeImmutable
     */
    protected $timestampX;

    /**
     * @var string
     */
    protected $TIMESTAMP_X;

    /**
     * @var DateTimeImmutable
     */
    protected $lastLogin;

    /**
     * @var string
     */
    protected $LAST_LOGIN;

    /**
     * @var DateTimeImmutable
     */
    protected $dateRegister;

    /**
     * @var string
     */
    protected $DATE_REGISTER;

    /**
     * @var DateTimeImmutable
     */
    protected $checkwordTime;

    /**
     * @var string
     */
    protected $CHECKWORD_TIME;

    /**
     * User constructor.
     *
     * @param array   $fields
     * @param boolean $useOriginal
     */
    public function __construct(array $fields = [], bool $useOriginal = false)
    {
        parent::__construct($fields, $useOriginal);
        $nonInitializedFields = $this->getNonInitializedFields();

        /**
         * Хеши пароля и проверочного слова необходимо обязательно брать в оригинальном виде,
         * без экранирования HTML-сущностей. Иначе хеш будет повреждён и не будет проходить проверки.
         */
        $passwordOriginalHashKey = '~PASSWORD';
        if (
            array_key_exists($passwordOriginalHashKey, $nonInitializedFields)
            && is_string($nonInitializedFields[$passwordOriginalHashKey])
        ) {
            $this->setPassword($nonInitializedFields[$passwordOriginalHashKey]);
            unset($nonInitializedFields[$passwordOriginalHashKey]);
        }

        $checkWordOriginalHashKey = '~CHECKWORD';
        if (
            array_key_exists($checkWordOriginalHashKey, $nonInitializedFields)
            && is_string($nonInitializedFields[$checkWordOriginalHashKey])
        ) {
            $this->setCheckword($nonInitializedFields[$checkWordOriginalHashKey]);
            unset($nonInitializedFields[$checkWordOriginalHashKey]);
        }

        $this->setNonInitializedFields($nonInitializedFields);
    }

    /**
     * @return array
     */
    public function getGroups(): array
    {
        //TODO Если это поле пустое, то оно должно быть запрошено из БД.

        return (array)$this->GROUP_ID;
    }

    /**
     * @param Collection|UserGroupInvolvement[] $groups
     *
     * @return $this
     */
    public function setGroups(Collection $groups): self
    {
        $this->GROUP_ID = [];

        foreach ($groups as $groupInvolvment) {
            if ($groupInvolvment instanceof UserGroupInvolvement) {
                $this->GROUP_ID[] = $groupInvolvment->toArray();
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return (string)$this->LAST_NAME;
    }

    /**
     * @param string $lastName
     *
     * @return $this
     */
    public function setLastName(string $lastName): self
    {
        $this->LAST_NAME = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getSecondName(): string
    {
        return (string)$this->SECOND_NAME;
    }

    /**
     * @param string $secondName
     *
     * @return $this
     */
    public function setSecondName(string $secondName): self
    {
        $this->SECOND_NAME = $secondName;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return (string)$this->EMAIL;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail(string $email): self
    {
        $this->EMAIL = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return (string)$this->LOGIN;
    }

    /**
     * @param string $login
     *
     * @return $this
     */
    public function setLogin(string $login): self
    {
        $this->LOGIN = $login;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->PASSWORD;
    }

    /**
     * @param string $password
     *
     * @return $this
     */
    public function setPassword(string $password): self
    {
        $this->PASSWORD = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getPasswordConfirm(): string
    {
        return $this->PASSWORD_CONFIRM;
    }

    /**
     * @param string $passwordConfirm
     *
     * @return $this
     */
    public function setPasswordConfirm(string $passwordConfirm): self
    {
        $this->PASSWORD_CONFIRM = $passwordConfirm;

        return $this;
    }

    /**
     * @return integer
     */
    public function getLoginAttemtps(): int
    {
        return (int)$this->LOGIN_ATTEMPTS;
    }

    /**
     * @param integer $loginAttempts
     *
     * @return $this
     */
    public function setLoginAttemtps(int $loginAttempts): self
    {
        $this->LOGIN_ATTEMPTS = $loginAttempts;

        return $this;
    }

    /**
     * @return string
     */
    public function getCheckword(): string
    {
        return (string)$this->CHECKWORD;
    }

    /**
     * @param string $checkword
     *
     * @return $this
     */
    public function setCheckword(string $checkword): self
    {
        $this->CHECKWORD = $checkword;

        return $this;
    }

    /**
     * @return string
     */
    public function getPersonalPhone(): string
    {
        return (string)$this->PERSONAL_PHONE;
    }

    /**
     * @param string $personalPhone
     *
     * @return $this
     */
    public function setPersonalPhone(string $personalPhone): self
    {
        $this->PERSONAL_PHONE = $personalPhone;

        return $this;
    }

    /**
     * @return null|string
     * @see \Prokl\BitrixOrmBundle\Base\Enum\Gender
     */
    public function getGender()
    {
        return $this->PERSONAL_GENDER;
    }

    /**
     * @param null|string $gender
     *
     * @return $this
     * @see \Prokl\BitrixOrmBundle\Base\Enum\Gender
     */
    public function setGender($gender): self
    {
        $this->PERSONAL_GENDER = $gender;

        return $this;
    }

    /**
     * @return null|DateTimeImmutable
     */
    public function getBirthday(): ?DateTimeImmutable
    {
        if (is_null($this->birthday) && trim($this->PERSONAL_BIRTHDAY) !== '') {
            $this->birthday = BitrixDateTimeConvert::bitrixStringDateTimeToDateTimeImmutable(
                $this->PERSONAL_BIRTHDAY
            );

            if ($this->birthday === false) {
                $this->birthday = null;
            }
        }

        return $this->birthday;
    }

    /**
     * @param null|DateTimeImmutable $birthday
     *
     * @return $this
     */
    public function setBirthday($birthday): self
    {
        if (!is_null($birthday) && !($birthday instanceof DateTimeImmutable)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expect birthday to be null|%s, but got %s',
                    DateTimeImmutable::class,
                    is_object($birthday) ? get_class($birthday) : gettype($birthday)
                )
            );
        }
        $this->birthday = $birthday;
        if (is_null($this->birthday)) {
            $this->PERSONAL_BIRTHDAY = $this->birthday;
        }
        if ($this->birthday instanceof DateTimeImmutable) {
            $this->PERSONAL_BIRTHDAY = BitrixDateTimeConvert::dateTimeImmutableToBitrixStringDateTime($this->birthday);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getWorkPhone(): string
    {
        return (string)$this->WORK_PHONE;
    }

    /**
     * @param string $workPhone
     *
     * @return $this
     */
    public function setWorkPhone(string $workPhone): self
    {
        $this->WORK_PHONE = $workPhone;

        return $this;
    }

    /**
     * @return string
     */
    public function getWorkPosition(): string
    {
        return (string)$this->WORK_POSITION;
    }

    /**
     * @param string $workPosition
     *
     * @return $this
     */
    public function setWorkPosition(string $workPosition): self
    {
        $this->WORK_POSITION = $workPosition;

        return $this;
    }

    /**
     * @return string
     */
    public function getWorkCompany(): string
    {
        return (string)$this->WORK_COMPANY;
    }

    /**
     * @param string $workCompany
     *
     * @return $this
     */
    public function setWorkCompany(string $workCompany): self
    {
        $this->WORK_COMPANY = $workCompany;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdminNotes(): string
    {
        return (string)$this->ADMIN_NOTES;
    }

    /**
     * @param string $adminNotes
     *
     * @return $this
     */
    public function setAdminNotes(string $adminNotes): self
    {
        $this->ADMIN_NOTES = $adminNotes;

        return $this;
    }

    /**
     * @return null|DateTimeImmutable
     */
    public function getTimestampX(): ?DateTimeImmutable
    {
        if (is_null($this->timestampX) && $this->TIMESTAMP_X !== '') {
            $this->timestampX = BitrixDateTimeConvert::bitrixStringDateTimeToDateTimeImmutable(
                (string)$this->TIMESTAMP_X
            );

            if ($this->timestampX === false) {
                $this->timestampX = null;
            }
        }

        return $this->timestampX;
    }

    /**
     * @param DateTimeImmutable $timestampX
     *
     * @return $this
     */
    public function setTimestampX(DateTimeImmutable $timestampX): self
    {
        $this->timestampX = $timestampX;
        $this->TIMESTAMP_X = BitrixDateTimeConvert::dateTimeImmutableToBitrixStringDateTime(
            $timestampX,
            'FULL'
        );

        return $this;
    }

    /**
     * @return null|DateTimeImmutable
     */
    public function getLastLogin()
    {
        if (is_null($this->lastLogin) && $this->LAST_LOGIN !== '') {
            $this->lastLogin = BitrixDateTimeConvert::bitrixStringDateTimeToDateTimeImmutable(
                (string)$this->LAST_LOGIN
            );
        }

        return $this->lastLogin;
    }

    /**
     * @param DateTimeImmutable $lastLogin
     *
     * @return $this
     */
    public function setLastLogin(DateTimeImmutable $lastLogin): self
    {
        $this->lastLogin = $lastLogin;
        $this->LAST_LOGIN = BitrixDateTimeConvert::dateTimeImmutableToBitrixStringDateTime(
            $lastLogin,
            'FULL'
        );

        return $this;
    }

    /**
     * @return null|DateTimeImmutable
     */
    public function getDateRegister()
    {
        if (is_null($this->dateRegister) && $this->DATE_REGISTER !== '') {
            $this->dateRegister = BitrixDateTimeConvert::bitrixStringDateTimeToDateTimeImmutable(
                (string)$this->DATE_REGISTER
            );
        }

        return $this->dateRegister;
    }

    /**
     * @param DateTimeImmutable $dateRegister
     *
     * @return $this
     */
    public function setDateRegister(DateTimeImmutable $dateRegister): self
    {
        $this->dateRegister = $dateRegister;
        $this->DATE_REGISTER = BitrixDateTimeConvert::dateTimeImmutableToBitrixStringDateTime(
            $dateRegister,
            'FULL'
        );

        return $this;
    }

    /**
     * @return null|DateTimeImmutable
     */
    public function getCheckwordTime(): DateTimeImmutable
    {
        if (is_null($this->checkwordTime) && $this->CHECKWORD_TIME !== '') {
            $this->checkwordTime = DateTimeImmutable::createFromFormat(
                self::CHECK_WORD_TIME_FORMAT,
                (string)$this->CHECKWORD_TIME
            );
        }

        return $this->checkwordTime;
    }

    /**
     * @param DateTimeImmutable $checkwordTime
     *
     * @return $this
     */
    public function setCheckwordTime(DateTimeImmutable $checkwordTime): self
    {
        $this->checkwordTime = $checkwordTime;
        $this->CHECKWORD_TIME = $checkwordTime->format(self::CHECK_WORD_TIME_FORMAT);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $fields = parent::toArray();

        /**
         * Для случая смены пароля должно быть задано оба поля,
         * иначе они будут удалены.
         */
        $passwordKey = 'PASSWORD';
        $passwordConfirmKey = 'PASSWORD_CONFIRM';
        if (
            !array_key_exists($passwordKey, $fields)
            || !array_key_exists($passwordConfirmKey, $fields)
            || is_null($fields[$passwordKey])
            || is_null($fields[$passwordConfirmKey])
        ) {
            unset($fields[$passwordKey], $fields[$passwordConfirmKey]);
        }

        /**
         * Если группы пользователя не заполнены, то они не участвуют в обновлении пользователя
         */
        $groupKey = 'GROUP_ID';
        if (array_key_exists($groupKey, $fields) && is_null($fields[$groupKey])) {
            unset($fields[$groupKey]);
        }

        /**
         * Исключить даты, устанавливаемые системой
         */
        $dateFieldKeyList = [
            'TIMESTAMP_X',
            'LAST_LOGIN',
            'DATE_REGISTER',
            'CHECKWORD_TIME',
        ];
        foreach ($dateFieldKeyList as $key) {
            if (array_key_exists($key, $fields)) {
                unset($fields[$key]);
            }
        }

        return $fields;
    }
}
