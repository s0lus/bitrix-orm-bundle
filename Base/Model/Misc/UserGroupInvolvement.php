<?php

namespace Prokl\BitrixOrmBundle\Base\Model\Misc;

use Prokl\BitrixOrmBundle\Base\Model\BitrixArrayItemBase;

/**
 * Class UserGroupInvolvement
 *
 * Вхождение пользователя в группу. Предназначено для указания включения пользователя в определённую группу на
 * определённый период при обновлении или создании пользователя. Не предназначено для получения информации о текущем
 * членстве в пользовательских группах.
 *
 * @package Prokl\BitrixOrmBundle\Base\\Model\Misc
 */
class UserGroupInvolvement extends BitrixArrayItemBase
{
    //TODO Добавить работу с DATE_ACTIVE_FROM и DATE_ACTIVE_TO

    /**
     * @var integer
     */
    protected $GROUP_ID;

    /**
     * @return integer
     */
    public function getId(): int
    {
        return (int)$this->GROUP_ID;
    }

    /**
     * @param integer $id
     *
     * @return $this
     */
    public function setId(int $id)
    {
        $this->GROUP_ID = $id;

        return $this;
    }

}
