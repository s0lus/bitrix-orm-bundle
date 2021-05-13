<?php

namespace Prokl\BitrixOrmBundle\Base\Type;

/**
 * Class ResultContent
 *
 * @package Prokl\BitrixOrmBundle\Base\\Type
 */
class ResultContent
{
    public const TYPE_SUCCESS = 'OK';

    public const TYPE_ERROR   = 'ERROR';

    /**
     * @var string Тип содержимого
     *
     * @see TextContent::TYPE_*
     */
    private $type = self::TYPE_SUCCESS;

    /**
     * @var string
     */
    private $message = '';

    /**
     * TextContent constructor.
     *
     * @param array $fields Поля.
     */
    public function __construct(array $fields = [])
    {
        if (isset($fields['TYPE'])) {
            $this->setType($fields['TYPE']);
        }

        if (isset($fields['MESSAGE'])) {
            $this->setMessage($fields['MESSAGE']);
        }
    }

    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * @param string $type Тип.
     *
     * @return ResultContent
     */
    public function setType(string $type) : ResultContent
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage() : string
    {
        return $this->message;
    }

    /**
     * @param string $message Сообщение.
     *
     * @return ResultContent
     */
    public function setMessage(string $message) : ResultContent
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isError() : bool
    {
        return $this->type === self::TYPE_ERROR;
    }

    /**
     * @return boolean
     */
    public function isSuccess() : bool
    {
        return $this->type === self::TYPE_SUCCESS;
    }
}
