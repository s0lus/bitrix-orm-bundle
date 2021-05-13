<?php

namespace Prokl\BitrixOrmBundle\Proxy;

use Prokl\BitrixOrmBundle\Base\Model\BitrixArrayItemBase;
use Prokl\BitrixOrmBundle\Cache\CacheInterface;
use Prokl\BitrixOrmBundle\Dto\ReflectionData;
use Prokl\BitrixOrmBundle\Enum\ReflectionEnum;
use Prokl\BitrixOrmBundle\Proxy\Traits\SourceRepoExtractorTrait;
use Doctrine\Common\Collections\ArrayCollection;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use function count;
use function get_class;
use function in_array;

/**
 * Class CacheProxy
 * @package Prokl\BitrixOrmBundle\Proxy
 */
class CacheProxy
{
    use SourceRepoExtractorTrait;

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var object
     */
    protected $subject;

    /**
     * @var object
     */
    protected $repository;

    /**
     * @var string[]
     */
    protected $excludedMethods;

    /**
     * @var ReflectionData[]
     */
    protected static $reflectionData = [];

    /**
     * CacheProxy constructor.
     *
     * @param string[] $excludedMethods
     */
    public function __construct(array $excludedMethods)
    {
        $this->excludedMethods = $excludedMethods;
    }

    /**
     * @param object $subject
     */
    public function setSubject($subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @param CacheInterface $cache
     */
    public function setCache(CacheInterface $cache): void
    {
        $this->cache = $cache;
    }

    /**
     * @param object $repository
     */
    public function setRepository($repository): void
    {
        $this->repository = $repository;
    }

    /**
     * @return object
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param mixed $name
     * @param mixed $arguments
     *
     * @return mixed
     * @throws ReflectionException
     */
    public function __call($name, $arguments)
    {
        $callback = function () use ($name, $arguments) {
            return [
                'result' => $this->subject->$name(...$arguments),
            ];
        };

        if (!$this->isExcludedMethod($name)) {
            $reflectionData = $this->getReflectionData($name);

            if ($reflectionData->getReturnType() === ReflectionEnum::RETURN_TYPE_MODEL &&
                $reflectionData->getArgumentType() === ReflectionEnum::ARGUMENT_TYPE_ID
            ) {
                $result = $this->cache->getById($arguments[0], $callback);
            } elseif ($reflectionData->getReturnType() === ReflectionEnum::RETURN_TYPE_MODEL &&
                $reflectionData->getArgumentType() === ReflectionEnum::ARGUMENT_TYPE_CODE
            ) {
                $result = $this->cache->getByCode($arguments[0], $callback);
            } elseif ($reflectionData->getReturnType() === ReflectionEnum::RETURN_TYPE_MODEL &&
                $reflectionData->getArgumentType() === ReflectionEnum::ARGUMENT_TYPE_XML_ID
            ) {
                $result = $this->cache->getByXmlId($arguments[0], $callback);
            } elseif ($reflectionData->getReturnType() === ReflectionEnum::RETURN_TYPE_COLLECTION &&
                $reflectionData->getArgumentType() === ReflectionEnum::ARGUMENT_TYPE_MODEL
            ) {
                $result = $this->cache->getByObject($arguments[0], $callback);
            } else {
                $result = $this->cache->get($name, $arguments, $callback);
            }
        } else {
            $result = $callback()['result'];
        }

        return $result;
    }

    /**
     * @param BitrixArrayItemBase $item
     *
     * @return mixed
     */
    public function add(BitrixArrayItemBase $item)
    {
        $result = $this->subject->add($item);

        $this->cache->set($item);

        return $result;
    }

    /**
     * @param BitrixArrayItemBase $item
     *
     * @return mixed
     */
    public function update(BitrixArrayItemBase $item)
    {
        $result = $this->subject->update($item);

        $this->cache->set($item);

        return $result;
    }

    /**
     * @param BitrixArrayItemBase $item
     *
     * @return mixed
     */
    public function delete(BitrixArrayItemBase $item)
    {
        $result = $this->subject->delete($item);

        $this->cache->clear($item->getId());

        return $result;
    }

    /**
     * @param integer $id
     *
     * @return mixed
     */
    public function deleteById(int $id)
    {
        $result = $this->subject->deleteById($id);

        $this->cache->clear($id);

        return $result;
    }

    /**
     * @param string $name
     *
     * @return boolean
     */
    protected function isExcludedMethod(string $name): bool
    {
        return in_array($name, $this->excludedMethods, true);
    }

    /**
     * @param string $name
     *
     * @return ReflectionData
     * @throws ReflectionException
     */
    protected function getReflectionData(string $name): ReflectionData
    {
        $key = get_class($this->repository) . '|' . $name;
        if (null === static::$reflectionData[$key]) {
            $reflection = new ReflectionClass($this->repository);
            $method     = $reflection->getMethod($name);

            $reflectionData = (new ReflectionData())->setArgumentType($this->getArgumentType($method))
                                                    ->setReturnType($this->getReturnType($method));

            static::$reflectionData[$key] = $reflectionData;
        }

        return static::$reflectionData[$key];
    }

    /**
     * @param ReflectionMethod $method
     *
     * @return string
     */
    protected function getArgumentType(ReflectionMethod $method): string
    {
        $result     = ReflectionEnum::ARGUMENT_TYPE_UNKNOWN;
        $parameters = $method->getParameters();

        if (1 === count($parameters)) {
            $parameter   = reset($parameters);
            $paramerName = mb_strtolower($parameter->getName());
            switch (true) {
                case 'id' === $paramerName:
                    $result = ReflectionEnum::ARGUMENT_TYPE_ID;
                    break;
                case 'code' === $paramerName:
                    $result = ReflectionEnum::ARGUMENT_TYPE_CODE;
                    break;
                case 'xmlid' === $paramerName:
                    $result = ReflectionEnum::ARGUMENT_TYPE_XML_ID;
                    break;
                /** @noinspection PhpPossiblePolymorphicInvocationInspection */
                case $parameter->getType() && is_a(
                        $parameter->getType()->getName(),
                        BitrixArrayItemBase::class,
                        true
                    ):
                    $result = ReflectionEnum::ARGUMENT_TYPE_MODEL;
                    break;
            }
        }

        return $result;
    }

    /**
     * @param ReflectionMethod $method
     *
     * @return string
     */
    protected function getReturnType(ReflectionMethod $method): string
    {
        $result = ReflectionEnum::RETURN_TYPE_UKNOWN;
        if ($returnType = $method->getReturnType()) {
            /** @noinspection PhpPossiblePolymorphicInvocationInspection */
            $name = $returnType->getName();

            switch ($name) {
                case is_a(
                    $name,
                    BitrixArrayItemBase::class,
                    true
                ):
                    $result = ReflectionEnum::RETURN_TYPE_MODEL;
                    break;
                case is_a(
                    $name,
                    ArrayCollection::class,
                    true
                ):
                    $result = ReflectionEnum::RETURN_TYPE_COLLECTION;
                    break;
            }
        }

        return $result;
    }
}
