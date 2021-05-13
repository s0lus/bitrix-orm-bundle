<?php

namespace Prokl\BitrixOrmBundle\Driver;

use Prokl\BitrixOrmBundle\Dto\Directory;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class DirectoryFinder
 * @package Prokl\BitrixOrmBundle\Driver
 */
class DirectoryFinder implements DirectoryFinderInterface
{
    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var string[] | null
     */
    private $directoryNames = [];

    /**
     * DirectoryFinder constructor.
     *
     * @param string $rootDir
     */
    public function __construct(string $rootDir)
    {
        $this->rootDir = $rootDir;
    }

    /**
     * @param string $name
     *
     * @return DirectoryFinderInterface
     */
    public function addName(string $name): DirectoryFinderInterface
    {
        $this->directoryNames[] = $name;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function find(): ArrayCollection
    {
        $finder = new Finder();
        $finder->directories()
               ->ignoreUnreadableDirs(true)
               ->in($this->rootDir);

        foreach ((array)$this->directoryNames as $name) {
            $finder->name($name);
        }
        $result = new ArrayCollection();

        /** @var SplFileInfo $fileInfo */
        foreach ($finder->getIterator() as $fileInfo) {
            $result->add(
                (new Directory())
                    ->setPath($fileInfo->getRealPath())
                    ->setName($fileInfo->getFilename())
            );
        }

        $result = new ArrayCollection(
            \array_merge(
                $result->toArray(),
                $this->getSubdirectories($result)->toArray()
            )
        );

        return $result;
    }

    /**
     * @param ArrayCollection $directories
     *
     * @return ArrayCollection
     */
    protected function getSubdirectories(ArrayCollection $directories): ArrayCollection
    {
        $result = new ArrayCollection();

        if (!$directories->isEmpty()) {
            $finder = new Finder();
            $finder->directories()
                   ->ignoreUnreadableDirs(true);

            /** @var Directory $dir */
            foreach ($directories as $dir) {
                $finder->in($dir->getPath());
            }

            /** @var SplFileInfo $fileInfo */
            foreach ($finder->getIterator() as $fileInfo) {
                $result->add(
                    (new Directory())
                        ->setPath($fileInfo->getRealPath())
                        ->setName($fileInfo->getFilename())
                );
            }
        }

        return $result;
    }
}
