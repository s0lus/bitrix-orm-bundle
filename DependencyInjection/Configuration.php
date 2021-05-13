<?php

namespace Prokl\BitrixOrmBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package Prokl\BitrixOrmBundle\DependencyInjection
 */
class Configuration  implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        if (!\method_exists(TreeBuilder::class, 'getRootNode')) {
            $treeBuilder = new TreeBuilder();
            $rootNode = $treeBuilder->root('bitrix_orm_bundle');
        } else {
            $treeBuilder = new TreeBuilder('bitrix_orm_bundle');
            $rootNode = $treeBuilder->getRootNode();
        }

        $rootNode
            ->children()
                ->arrayNode('bitrix_orm_namespaces')
                ->defaultValue(['Local' => 'local\classes'])
                ->useAttributeAsKey('name')
                ->prototype('array')
            ->end()->end()
                ->arrayNode('bitrix_orm_namespaces_extended')
                ->defaultValue([])
                ->useAttributeAsKey('name')
                ->prototype('array')
            ->end()
        ;

        return $treeBuilder;
    }
}
