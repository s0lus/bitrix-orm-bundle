<?php

namespace Prokl\BitrixOrmBundle\Base\Model;

use InvalidArgumentException;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\BitrixArrayItemInterface;
use Prokl\BitrixOrmBundle\Base\Model\Traits\HasIdTrait;

/**
 * Class BitrixArrayItemBase
 *
 * Создайте свойство класса с именем `FIELD` , чтобы поле `FIELD` было заполнено из массива с ключом `FIELD`. При этом
 * работа со свойствами элемента инфоблока ведётся при создании свойства класса `PROPERTY_*_VALUE` , что позволяет не
 * использовать никакие выражения проверки при инициализации объекта.
 *
 * @internal Незаполненное свойство элемента инфоблока имеет значение false и при инициализации такое значение типа
 * bool попадает в свойство класса, которое может иметь другой тип (int, string,...), поэтому в геттерах рекомендуется
 * выполнять приведение типа, чтобы избежать ошибки несоответствия возвращаемого методом типа.
 *
 * @package Prokl\BitrixOrmBundle\Base\\Model
 */
abstract class BitrixArrayItemBase implements BitrixArrayItemInterface
{
    /**
     * TODO Рассмотреть перенос этого трейта ниже, в новый класс Item, чтобы сущности связки "многие-ко-многим" не
     *  имели getId(), ведь у них будет свой набор полей. Например, userId + groupId.
     */
    use HasIdTrait;

    const TILDA_PREFIX = '~';

    /**
     * @var array Неинициализированные поля: позволяет передавать из родительского конструктора в дочерний только
     *     те поля, которые не были обработаны, что позволяет избежать многократного обхода всего массива полей. При
     *     наличии длинной цепочки наследования и большом количестве полей позволяет улучшить производительность.
     */
    protected $nonInitializedFields = [];

    /**
     * BitrixArrayItemBase constructor.
     *
     * @param array $fields
     * @param boolean $useOriginal Использовать оригинальные, не HTML-безопасные значения полей из ключей с префиксом
     *     TILDA_PREFIX
     */
    public function __construct(array $fields = [], bool $useOriginal = false)
    {
        $nonInitializedFields = [];

        if ($useOriginal) {
            $fieldsToHandle = $this->filterOriginalValues($fields);
        } else {
            $fieldsToHandle = $fields;
        }

        foreach ($fieldsToHandle as $field => $value) {
            if (property_exists($this, $field)) {
                $this->{$field} = $value;
            } else {
                $nonInitializedFields[$field] = $value;
            }
        }

        /**
         * Чтобы дочерние конструкторы могли избежать повторной проверки уже обработанных полей.
         * Для этого в конструкторе дочернего класса вызов конструктора родителя должен стоять перед проверкой полей.
         */
        $this->setNonInitializedFields($nonInitializedFields);
    }

    /**
     * @param array $fields
     *
     * @param boolean $useOriginal
     *
     * @return static
     */
    public static function createFromArray(array $fields = [], bool $useOriginal = false): BitrixArrayItemInterface
    {
        return new static($fields, $useOriginal);
    }

    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        $asArray = [];

        foreach (get_object_vars($this) as $field => $value) {
            /**
             * Если имя поля начинается с большой буквы,
             * то попадает в представление в виде массива.
             */
            $fieldFirstSymbol = mb_substr($field, 0, 1);
            if ($fieldFirstSymbol >= 'A' && $fieldFirstSymbol <= 'Z') {
                $asArray[$field] = $value;
            }
        }

        return $asArray;
    }

    /**
     * @return array
     */
    protected function getNonInitializedFields(): array
    {
        return $this->nonInitializedFields;
    }

    /**
     * @param array $nonInitializedFields
     *
     * @return $this
     */
    protected function setNonInitializedFields(array $nonInitializedFields)
    {
        $this->nonInitializedFields = $nonInitializedFields;

        return $this;
    }

    /**
     * Возвращает поля с оригинальными (не HTML-безопасными) значениями, взятыми из полей с тильдой `~`.
     *
     * @param array $input Массив вида ['FIELD' => 'html-safe value', '~FIELD' => 'original value']
     *
     * @return array ['FIELD' => 'original value']
     */
    private function filterOriginalValues(array $input): array
    {
        $output = [];

        foreach ($input as $field => $value) {

            if (mb_substr($field, 0, 1) === self::TILDA_PREFIX) {
                continue;
            }

            $tildaField = self::TILDA_PREFIX . $field;
            if (!array_key_exists($tildaField, $input)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Key `%s` was not found in the input array',
                        $tildaField
                    )
                );
            }

            $output[$field] = $input[$tildaField];
        }

        return $output;
    }
}
