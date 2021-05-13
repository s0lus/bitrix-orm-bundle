<?php

namespace Prokl\BitrixOrmBundle\Base\Model;

use Prokl\BitrixOrmBundle\Base\Enum\Module;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\FileRepositoryAwareInterface;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\HasActiveInterface;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\HasCodeInterface;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\HasIblockIdInterface;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\HasIblockSectionIdInterface;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\HasListPageUrlInterface;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\HasNameInterface;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\HasSortInterface;
use Prokl\BitrixOrmBundle\Base\Model\Interfaces\HasXmlIdInterface;
use Prokl\BitrixOrmBundle\Base\Model\Iprop\IpropElement;
use Prokl\BitrixOrmBundle\Base\Model\Traits\FileRepositoryAwareTrait;
use Prokl\BitrixOrmBundle\Base\Model\Traits\HasActiveAsStringTrait;
use Prokl\BitrixOrmBundle\Base\Model\Traits\HasCodeTrait;
use Prokl\BitrixOrmBundle\Base\Model\Traits\HasIblockIdTrait;
use Prokl\BitrixOrmBundle\Base\Model\Traits\HasIblockSectionIdTrait;
use Prokl\BitrixOrmBundle\Base\Model\Traits\HasListPageUrlTrait;
use Prokl\BitrixOrmBundle\Base\Model\Traits\HasNameTrait;
use Prokl\BitrixOrmBundle\Base\Model\Traits\HasSortTrait;
use Prokl\BitrixOrmBundle\Base\Model\Traits\HasXmlIdTrait;
use Prokl\BitrixOrmBundle\Base\Type\TextContent;
use Bitrix\Iblock\InheritedProperty\ElementValues;
use CIBlockElement;
use DateTimeImmutable;
use Exception;
use Prokl\BitrixOrmBundle\Tools\Convertors\BitrixDateTimeConvert;

/**
 * Class IblockElement
 * @package Prokl\BitrixOrmBundle\Base\\Model
 */
abstract class IblockElement extends BitrixArrayItemBase implements
    HasActiveInterface,
    HasCodeInterface,
    HasIblockIdInterface,
    HasIblockSectionIdInterface,
    HasListPageUrlInterface,
    HasNameInterface,
    HasSortInterface,
    HasXmlIdInterface,
    FileRepositoryAwareInterface
{
    use HasActiveAsStringTrait;
    use HasCodeTrait;
    use HasIblockIdTrait;
    use HasIblockSectionIdTrait;
    use HasListPageUrlTrait;
    use HasNameTrait;
    use HasSortTrait;
    use HasXmlIdTrait;
    use FileRepositoryAwareTrait;

    const PROPERTY_VALUES = 'PROPERTY_VALUES';

    /**
     * Маска проверки имени поля на соответствие свойству элемента инфоблока.
     * @todo проверить, убрать захват на 1 и 2 скобке
     */
    const PATTERN_PROPERTY_VALUE = '~^(?>(PROPERTY_([-\w]+))_VALUE)$~';

    /**
     * @var string
     */
    protected $PREVIEW_TEXT;

    /**
     * @var string
     */
    protected $PREVIEW_TEXT_TYPE;

    /**
     * @var TextContent
     */
    protected $previewText;

    /**
     * @var array|int
     */
    protected $PREVIEW_PICTURE;

    /**
     * @var File
     */
    protected $previewPicture;

    /**
     * @var string
     */
    protected $DETAIL_TEXT;

    /**
     * @var string
     */
    protected $DETAIL_TEXT_TYPE;

    /**
     * @var TextContent
     */
    protected $detailText;

    /**
     * @var array|int
     */
    protected $DETAIL_PICTURE;

    /**
     * @var File
     */
    protected $detailPicture;

    /**
     * @var string
     */
    protected $DETAIL_PAGE_URL;

    /**
     * @var string
     */
    protected $CANONICAL_PAGE_URL;

    /**
     * @var string
     */
    protected $DATE_ACTIVE_FROM;

    /**
     * @var string
     */
    protected $DATE_ACTIVE_TO;

    /**
     * @var DateTimeImmutable
     */
    protected $dateActiveFrom;

    /**
     * @var DateTimeImmutable
     */
    protected $dateActiveTo;

    /**
     * @var int[] ID всех разделов инфоблока, к которым прикреплён элемент.
     */
    protected $sectionIdList;

    /**
     * @var IpropElement
     */
    protected $iProperty;

    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        $asArray = [];
        $props = [];
        $matches = [];

        foreach (parent::toArray() as $field => $value) {
            if (1 === preg_match(self::PATTERN_PROPERTY_VALUE, $field, $matches)) {
                //TODO Если свойство множественное и его значение пустое, то должен передаваться не array(), а false.
                //TODO Для свойства типа Список следует передавать идентификатор значения свойства, а не значение.
                //TODO Тип "файл" тоже надо по-особенному обрабатывать. Особенно с учётом проверки при включённом модуле документоооборота.
                $props[$matches[2]] = $value;
            } elseif (
                ($field === 'DETAIL_PICTURE' || $field === 'PREVIEW_PICTURE')
                && is_numeric($value)
                && (int)$value > 0
            ) {
                /**
                 * См. пояснения в теле метода \BitrixOrm\Model\File::getValueForSaving
                 */
                $asArray[$field] = (new File())->setModuleId(Module::IBLOCK)
                                               ->setId((int)$value)
                                               ->getValueForSaving();
            } else {
                $asArray[$field] = $value;
            }
        }

        if (count($props) > 0) {
            $asArray[self::PROPERTY_VALUES] = $props;
        }

        return $asArray;
    }

    /**
     * @return string
     */
    public function getDetailPageUrl(): string
    {
        return (string)$this->DETAIL_PAGE_URL;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setDetailPageUrl(string $url)
    {
        $this->DETAIL_PAGE_URL = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getCanonicalPageUrl(): string
    {
        return (string)$this->CANONICAL_PAGE_URL;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setCanonicalPageUrl(string $url)
    {
        $this->CANONICAL_PAGE_URL = $url;

        return $this;
    }

    /**
     * @return TextContent
     */
    public function getPreviewText(): TextContent
    {
        if (is_null($this->previewText)) {
            $this->previewText = (new TextContent())->setText((string)$this->PREVIEW_TEXT)
                                                    ->setType((string)$this->PREVIEW_TEXT_TYPE);
        }

        return $this->previewText;
    }

    /**
     * @param TextContent $previewText
     *
     * @return $this
     */
    public function setPreviewText(TextContent $previewText)
    {
        $this->previewText = $previewText;
        $this->PREVIEW_TEXT_TYPE = $previewText->getType();
        $this->PREVIEW_TEXT = $previewText->getText();

        return $this;
    }

    /**
     * @return TextContent
     */
    public function getDetailText(): TextContent
    {
        if (is_null($this->detailText)) {
            $this->detailText = (new TextContent())->setText((string)$this->DETAIL_TEXT)
                                                   ->setType((string)$this->DETAIL_TEXT_TYPE);
        }

        return $this->detailText;
    }

    /**
     * @param TextContent $detailText
     *
     * @return $this
     */
    public function setDetailText(TextContent $detailText)
    {
        $this->detailText = $detailText;
        $this->DETAIL_TEXT_TYPE = $detailText->getType();
        $this->DETAIL_TEXT = $detailText->getText();

        return $this;
    }

    /**
     * @return null|DateTimeImmutable
     */
    public function getDateActiveFrom(): ?DateTimeImmutable
    {
        if ($this->DATE_ACTIVE_FROM !== '' || is_null($this->dateActiveFrom)) {
            $this->dateActiveFrom = BitrixDateTimeConvert::bitrixStringDateTimeToDateTimeImmutable(
                (string)$this->DATE_ACTIVE_FROM
            );

            if ($this->dateActiveFrom === false) {
                $this->dateActiveFrom = null;
            }

        }

        return $this->dateActiveFrom;
    }

    /**
     * @param DateTimeImmutable $dateActiveFrom
     *
     * @return $this
     */
    public function setDateActiveFrom(DateTimeImmutable $dateActiveFrom)
    {
        $this->dateActiveFrom = $dateActiveFrom;
        $this->DATE_ACTIVE_FROM = BitrixDateTimeConvert::dateTimeImmutableToBitrixStringDateTime(
            $dateActiveFrom,
            'FULL'
        );

        return $this;
    }

    /**
     * @return null|DateTimeImmutable
     */
    public function getDateActiveTo()
    {
        if (is_null($this->dateActiveTo) && $this->DATE_ACTIVE_TO !== '') {
            $this->dateActiveTo = BitrixDateTimeConvert::bitrixStringDateTimeToDateTimeImmutable(
                (string)$this->DATE_ACTIVE_TO
            );

            if ($this->dateActiveTo === false) {
                $this->dateActiveTo = null;
            }
        }

        return $this->dateActiveTo;
    }

    /**
     * @param DateTimeImmutable $dateActiveTo
     *
     * @return $this
     */
    public function setDateActiveTo(DateTimeImmutable $dateActiveTo)
    {
        $this->dateActiveTo = $dateActiveTo;
        $this->DATE_ACTIVE_TO = BitrixDateTimeConvert::dateTimeImmutableToBitrixStringDateTime($dateActiveTo, 'FULL');

        return $this;
    }

    /**
     * Проверяет, активен ли элемент на основании дат начала и окончания активности.
     *
     * @param null|DateTimeImmutable $checkDate Дата, относительно которой производится проверка. Если null, то
     *     проверка происходит по текущей дате.
     *
     *
     *
     * @throws Exception
     * @return boolean
     */
    public function isActiveByDates(DateTimeImmutable $checkDate = null): bool
    {
        if (is_null($checkDate)) {
            $checkDate = new DateTimeImmutable();
        }

        if (
            $this->getDateActiveFrom() instanceof DateTimeImmutable
            && $this->getDateActiveFrom()->diff($checkDate)->invert === 1
        ) {
            return false;
        }

        if (
            $this->getDateActiveTo() instanceof DateTimeImmutable
            && $checkDate->diff($this->getDateActiveTo())->invert === 1
        ) {
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function getSectionsIdList(): array
    {
        if (
            is_null($this->sectionIdList)
            || (is_array($this->sectionIdList) && count($this->sectionIdList) === 0)
        ) {
            $this->sectionIdList = [];
            $dbSectionList = CIBlockElement::GetElementGroups($this->getId(), true, ['ID']);

            while ($section = $dbSectionList->Fetch()) {
                $this->sectionIdList[] = (int)$section['ID'];
            }
        }

        return $this->sectionIdList;
    }

    /**
     * @return integer
     */
    public function getPreviewPictureId(): int
    {
        return $this->getPreviewPicture()->getId();
    }

    /**
     * @param integer $id
     *
     * @return $this
     */
    public function setPreviewPictureId(int $id)
    {
        $this->PREVIEW_PICTURE = $id;
        $this->previewPicture = null;

        return $this;
    }

    /**
     * @return integer
     */
    public function getDetailPictureId(): int
    {
        return $this->getDetailPicture()->getId();
    }

    /**
     * @param integer $id
     *
     * @return $this
     */
    public function setDetailPictureId(int $id)
    {
        $this->DETAIL_PICTURE = $id;
        $this->detailPicture = null;

        return $this;
    }

    /**
     * @return File
     */
    public function getPreviewPicture(): File
    {
        if (is_null($this->previewPicture)) {
            $this->previewPicture = (self::getFileRepository())->getFileById((int)$this->PREVIEW_PICTURE);
        }

        return $this->previewPicture;
    }

    /**
     * @param File $previewPicture
     *
     * @return $this
     */
    public function setPreviewPicture(File $previewPicture)
    {
        $this->previewPicture = $previewPicture;
        $this->PREVIEW_PICTURE = $previewPicture->getValueForSaving();

        return $this;
    }

    /**
     * @return File
     */
    public function getDetailPicture(): File
    {
        if (is_null($this->detailPicture)) {
            $this->detailPicture = (self::getFileRepository())->getFileById((int)$this->DETAIL_PICTURE);
        }

        return $this->detailPicture;
    }

    /**
     * @param File $detailPicture
     *
     * @return $this
     */
    public function setDetailPicture(File $detailPicture)
    {
        $this->detailPicture = $detailPicture;
        $this->DETAIL_PICTURE = $detailPicture->getValueForSaving();

        return $this;
    }

    /**
     * @return IpropElement
     */
    public function getIProperty(): IpropElement
    {
        if (is_null($this->iProperty)) {
            $this->iProperty = new IpropElement(new ElementValues($this->getIblockId(), $this->getId()));
        }

        return $this->iProperty;
    }

    /**
     * @param IpropElement $iProperty
     */
    public function setIProperty(IpropElement $iProperty)
    {
        $this->iProperty = $iProperty;
    }
}
