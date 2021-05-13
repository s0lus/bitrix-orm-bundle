# Bitrix ORM bundle

[Форк](https://gitlab.kant.ru/kantecom/bitrix-orm-bundle). Упростил маленько, избавился от лишних зависимостей, прикрутил другой
кэшер и т.п..

Интересный пример работы Symfony + Битрикс. Но на мой взгляд, перекручено с абстракциями. 
Что не отменяет качественной работы.

## Оригинальная документация

## Оглавление
* [Установка](#installation)
* [Функциональность.](#functionality)
* [Список доступных аннотаций.](#annotations)
  * [Фабрики и репозитории](#annotations-factories-repositories)
      * [Элемент инфоблока - `IblockElement`](#annotations-iblockelement)
      * [Раздел инфоблока - `IblockSection`](#annotations-iblocksection)
      * [D7-сущность - `D7Item`](#annotations-d7item)
      * [Элемент HL-блока - `HlbItem`](#annotations-hlbitem)
      * [Элемент справочник - `HlbReferenceItem`](#annotations-hlbreferenceitem)
      * [Тип цен - `CatalogGroup`](#annotations-cataloggroup)
  * [Кеширование](#annotations-cache)
      * [Кеширование объектов](#annotations-array-cache)
      * [Кеширование Bitrix](#annotations-bitrix-cache)
  * [Гидратация объектов](#annotations-hydrator)
      * [Hydrator](#annotations-hydrator-hydrator)
* [RepositoryRegistry](#repositoryregistry)
* [Примеры использования](#examples)
  * [Элемент HL-блока](#examples-hlbitem)
  * [Элемент инфоблока](#examples-iblockelement)
  * [Тип цен](#examples-cataloggroup)
  * [RepositoryRegistry](#examples-repositoryregistry)

<a name="installation"></a>
## Установка

Добавить в `composer.json`:

```json
  "repositories": [
    {
      "type": "git",
      "url": "https://github.com/proklung/bitrix-orm-bundle"
    }
  ]

```

Выполнить:

```bash
$ composer require proklung/bitrix-orm-bundle
```

<a name="functionality"></a>
## Функциональность

* Определение необходимых параметров моделей bitrix-orm через аннотации
* Автоматический поиск моделей, имеющих аннотации, поддерживаемые бандлом
* Автоматическая регистрация репозиториев как сервисов
* Автоматическое заполнение зависимостей модели
* Кеширование объектов в памяти (использование везде одного и того же объекта)
* Встроенное кеширование битрикс (в т.ч. с использованием тегов)

<a name="annotations"></a>
## Список доступных аннотаций

Файлы с аннотациями подгружаются из всех папок в директории `src` проекта, имеющих название `Model` (и их поддиректориях).

<a name="annotations-factories-repositories"></a>
### Фабрики и репозитории

Каждая аннотация имеет параметры:

* `factory` - класс фабрики моделей. Обязательный параметр
* `repository` - класс репозитория. Является обязательным для всех аннотаций,
кроме `CatalogGroup` и `HlbReference`, что классы репозиториев для остальных моделей,
указанные по умолчанию, являются абстрактными

<a name="annotations-iblockelement"></a>
##### 1. IblockElement
Аннотация для элементов инфоблоков

Список параметров:
* `iblockType` - тип инфоблока (`IBLOCK_TYPE_ID`). Обязательный параметр
* `iblockCode` - код инфоблока (`CODE`). Обязательный параметр

<a name="annotations-iblocksection"></a>
##### 2. IblockSection
Аннотация для разделов инфоблоков

Список параметров:
* `iblockType` - тип инфоблока (`IBLOCK_TYPE_ID`). Обязательный параметр
* `iblockCode` - код инфоблока (`CODE`). Обязательный параметр

<a name="annotations-d7item"></a>
##### 3. D7Item
Аннотация для собственных таблиц

Список параметров:
* `table` - класс, расширяющий `Bitrix\Main\Entity\DataManager`. Обязательный параметр

<a name="annotations-hlbitem"></a>
##### 4. HlbItem
Аннотация для элементов HL-блоков

Список параметров:
* `hlBlockName` - Имя HL-блока, которому принадлежит данная модель. Обязательный параметр
* `entityCache` (bool) - Использование хака, ускоряющего инициализацию hl-блоков путем кеширования hlblockentity. По умолчанию включен

<a name="annotations-hlbreferenceitem"></a>
##### 5. HlbReferenceItem
Аннотация для элементов  HL-блоков-справочников

Список параметров:
* `hlBlockName` - Имя HL-блока, которому принадлежит данная модель. Обязательный параметр
* `entityCache` (bool) - Использование хака, ускоряющего инициализацию hl-блоков путем кеширования hlblockentity. По умолчанию включен

<a name="annotations-cataloggroup"></a>
##### 6. CatalogGroup
Аннотация для типов цен. Дополнительных параметров нет.





  * [Кеширование](#annotations-cache)
      * [Кеширование объектов](#annotations-array-cache)
      * [Кеширование Bitrix](#annotations-bitrix-cache)
  * [Гидратация объектов](#annotations-hydrator)


<a name="annotations-cache"></a>
### Кеширование
Данные аннотации работают только тогда, когда задана аннотация "фабрики и репозитория".
При указании этих аннотаций регистрируются сервисы, являющиеся слоями кеширования.
`RepositoryRegistry` в таком случае возвращает данный сервис вместо репозитория.
Работают данные слои на `ReflectionClass` следующим образом:
1) Заданы методы `add`, `update`, `delete`, `deleteById`, которые проксируются в репозиторий.
При этом в объектном кеше удаляется/обновляется/добавляется элемент
2) Для остальных методов: 
  * Если заданы `excludedMethods`, то данные методы просто проксируются в репозиторий
  * Происходит анализ входных параметров с помощью `\ReflectionMethod`, вызов метода, кеширование результата:
    * Если количество параметров == 1, то
      * Если имя параметра `id`, то метод считается "получением элемента по ID" для BitrixCache на результат вешается `tag . ':' . $id`
      * Если имя параметра `xmlId`, то метод считается "получением элемента по XML_ID" для BitrixCache на результат вешается `tag . ':' . $xmlId`
      * Если имя параметра `code`, то метод считается "получением элемента по CODE", для BitrixCache на результат вешается `tag . ':' . $code`
      * Если параметр является объектом `BitrixArrayItemBase`, то метод считается "получением объекта/ов по родительскому объекту" (цен по офферу, остатков по офферу), для BitrixCache на результат вешается `collectionTag . ':' . $item->getId()`
    * Если параметров != 1, то происходит кеширование по ключу кеша = \get_class($model) . json_encode($parameters)

Можно указывать и `Cache` и `BitrixCache`, либо один из них, либо вовсе не указывать

<a name="annotations-array-cache"></a>
##### 1. Cache

Параметры:

* `class` - класс, реализующий `KantShop\BitrixOrmBundle\Cache\CacheInterface` (`ArrayCache` по умолчанию)
* `excludedMethods` - массив методов репозитория, результаты которых кешироваться не будут

<a name="annotations-bitrix-cache"></a>
##### 2. BitrixCache

* `class` - класс, реализующий `KantShop\BitrixOrmBundle\Cache\BitrixCacheInterface` (`BitrixCache` по умолчанию)
* `excludedMethods` - массив методов репозитория, результаты которых кешироваться не будут
* `tag` - тег кеша
* `collectionTag` - тег коллекции элементов
* `cacheTime` - время кеширования (1 час по умолчанию)

<a name="annotations-hydrator"></a>
### Гидратация объектов

Данные аннотации работают только тогда, когда задана аннотация "фабрики и репозитория".
При указании этих аннотаций регистрируются сервисы, которые заполняют необходимые данные для модели.
Гидратор вызывается после получения данных из репозитория/кеша. Для каждого инстанса модели он вызывается один раз (используется \SplObjectStorage).
Если репозиторий вернул `ArrayCollection`, то гидратор вызывается для каждого элемента поочередно.

<a name="annotations-hydrator-hydrator"></a>
##### 1. Hydrator

Параметры:

* `class` - класс, реализующий `KantShop\BitrixOrmBundle\HYdrator\HydratorInterface`

<a name="repositoryregistry"></a>
## RepositoryRegistry

Сервис, позволяющий получить нужный репозиторий по классу модели

```php
<?php

use Acme\AppBundle\Model\City;

$registry = $serviceContainer->get('bitrix_orm.repository_registry');
$cityRepository = $registry->get(City::class);

```

<a name="examples"></a>
## Примеры использования

<a name="examples-hlbitem"></a>
##### Элемент HL-блока

Модель:

```php
<?php

namespace Acme\AppBundle\Model;

use KantShop\BitrixOrm\Model\D7Item;
use KantShop\BitrixOrmBundle\Annotation as ORM;
use Acme\AppBundle\Repository\CityRepository;
use Acme\AppBundle\Factory\CityFactory;
use Acme\AppBundle\Hydrator\CityHydrator;

/**
 * @ORM\HlbItem(
 *     hlBlockName="Cities",
 *     repository=CityRepository::class,
 *     factory=CityFactory::class
 * )
 * @ORM\BitrixCache(tag="city",cacheTime=3600)
 * @ORM\Cache()
 * @ORM\Hydrator(class=CityHydrator::class)
 */
class City extends D7Item
{
    // необходимые свойства модели
}
```

Фабрика:

```php
<?php

namespace Acme\AppBundle\Factory;

use Acme\AppBundle\Model\City;
use KantShop\BitrixOrm\Factories\D7ItemFactory;
use KantShop\BitrixOrm\Model\D7Item;

class CityFactory extends D7ItemFactory
{
    public function createItem(array $data): D7Item
    {
        return new City($data);
    }
    
    
    public function getSelect(): array
    {
        return ['*'];
    }
}
```

Репозиторий:

```php
<?php

namespace Acme\AppBundle\Repository;

use KantShop\BitrixOrm\Repository\D7Repository;

class CityRepository extends D7Repository
{

}

```

Гидратор:

```php
<?php

namespace Acme\AppBundle\Hydrator;

use Acme\AppBundle\Model\City;
use Acme\AppBundle\Service\StreetProvider;
use KantShop\BitrixOrmBundle\Hydrator\HydratorInterface;

class CityHydrator implements HydratorInterface
{
    /**
     * @var StreetProvider
     */
    protected $streetProvider;
    
    public function __construct(StreetProvider $streetProvider) {
        $this->streetProvider = $streetProvider;
    }

    /**
     * @param City $object
     * @return City
     */
    public function fill($object){
        $object->setStreets($this->streetProvider->getCityStreets($object));
        return $object;
    }
}

```

Сервис:

```php
<?php

namespace Acme\AppBundle\Service;

use Acme\AppBundle\Model\City;
use KantShop\BitrixOrmBundle\Registry\RepositoryRegistryInterface;

class CityService
{
    protected $repository;
    
    public function __construct(RepositoryRegistryInterface $repository)
    {
        $this->repository = $repository->get(City::class);
    }
}
```

<a name="examples-iblockelement"></a>
##### Элемент инфоблока

Модель:
```php
<?php

namespace Acme\AppBundle\Model;

use KantShop\BitrixOrm\Model\IblockElement;
use KantShop\BitrixOrmBundle\Annotation as ORM;

/**
 * Class Product
 * @package Acme\AppBundle\Model
 *
 * @ORM\IblockElement(
 *     iblockType="catalog",
 *     iblockCode="products",
 *     factory="Acme\AppBundle\Factory\ProductFactory",
 *     repository="Acme\AppBundle\Repository\ProductRepository",
 * )
 */
class Product extends IblockElement
{
    // ...
}

```

Можно указать `iblockСode` и `iblockType`, используя константы:

```php
<?php

namespace Acme\AppBundle\Model;

use KantShop\BitrixOrm\Model\IblockElement;
use KantShop\BitrixOrmBundle\Annotation as ORM;
use Acme\AppBundle\Enum\IblockType;
use Acme\AppBundle\Enum\IblockCode;

/**
 * Class Product
 * @package Acme\AppBundle\Model
 *
 * @ORM\IblockElement(
 *     iblockType=IblockType::CATALOG,
 *     iblockCode=IblockCode::PRODUCTS,
 *     factory="Acme\AppBundle\Factory\ProductFactory",
 *     repository="Acme\AppBundle\Repository\ProductRepository",
 * )
 */
class Product extends IblockElement
{
    // ...
}

```

Репозиторий:

```php
<?php

namespace Acme\AppBundle\Repository;

use KantShop\BitrixOrm\Repository\IblockElementRepository;

class ProductRepository extends IblockElementRepository
{

}

```

<a name="examples-cataloggroup"></a>
##### Тип цен

Модель:

```php
<?php

namespace Acme\AppBundle\Model;

use KantShop\BitrixOrm\Model\CatalogGroup as BaseCatalogGroup;
use KantShop\BitrixOrmBundle\Annotation as ORM;

/**
 * Class CatalogGroup
 * @package Acme\AppBundle\Model
 *
 * @ORM\CatalogGroup()
 */
class CatalogGroup extends BaseCatalogGroup
{

}

```

<a name="examples-repositoryregistry"></a>
##### Автовайринг RepositoryRegistry

```php
<?php

namespace Acme\AppBundle\Service;

use Acme\AppBundle\Model\City;
use Acme\AppBundle\Repository\CityRepository;
use KantShop\BitrixOrmBundle\Registry\RepositoryRegistryInterface;

class CityService
{
    /**
     * @var CityRepository
     */
    protected $repository;

    /**
     * @var FileRepository
     */
    protected $fileRepository;

    public function __construct(RepositoryRegistryInterface $registry)
    {
        $this->repository = $registry->get(City::class);
        $this->fileRepository = $registry->get(File::class);
    }
}
```
