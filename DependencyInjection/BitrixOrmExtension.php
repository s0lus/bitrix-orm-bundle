<?php

namespace Prokl\BitrixOrmBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class BitrixOrmExtension
 * @package Prokl\BitrixOrmBundle\DependencyInjection
 */
class BitrixOrmExtension extends Extension
{
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yml');

        if (count($config['bitrix_orm_namespaces']) > 0) {
            $container->setParameter('bitrix_orm.namespaces', $config['bitrix_orm_namespaces']);
        }

        if (count($config['bitrix_orm_namespaces_extended']) > 0) {
            $container->setParameter('bitrix_orm.namespaces_extended', $config['bitrix_orm_namespaces_extended']);
        }
    }

    /**
     * @inheritDoc
     */
    public function getAlias() : string
    {
        return 'bitrix_orm';
    }
}
