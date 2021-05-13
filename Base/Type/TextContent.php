<?php

namespace Prokl\BitrixOrmBundle\Base\Type;

use function html_entity_decode;

/**
 * Class TextContent
 * @package Prokl\BitrixOrmBundle\Base\Type
 */
class TextContent
{
    public const TYPE_HTML = 'html';

    public const TYPE_TEXT = 'text';

    /**
     * @var string Тип содержимого
     * @see TextContent::TYPE_*
     */
    protected $type = self::TYPE_HTML;

    /**
     * @var string $text
     */
    protected $text = '';

    /**
     * TextContent constructor.
     *
     * @param mixed $fields Поля.
     */
    public function __construct($fields = null)
    {
        if (is_array($fields) && isset($fields['TYPE'], $fields['TEXT'])) {
            $this->setType($fields['TYPE'])
                 ->setText($fields['TEXT']);

        }
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        if ($this->matchType(self::TYPE_HTML)) {
            return html_entity_decode($this->text);
        }

        return $this->text;
    }

    /**
     * @param string $text
     *
     * @return TextContent
     */
    public function setText(string $text): TextContent
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @param string $type
     *
     * @return boolean
     */
    private function matchType(string $type): bool
    {
        return strtolower($this->getType()) === $type;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return TextContent
     */
    public function setType(string $type): TextContent
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getText();
    }
}
