<?php

namespace Prokl\BitrixOrmBundle;

use Prokl\BitrixOrmBundle\DependencyInjection\Compiler\EntityAnnotationsProcessor;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class BitrixOrmBundle
 * @package Prokl\BitrixOrmBundle
 */
class BitrixOrmBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     *
     * @throws \ReflectionException
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(
            new EntityAnnotationsProcessor(),
            PassConfig::TYPE_BEFORE_OPTIMIZATION,
            100
        );
    }
}
