<?php

namespace Mrsuh\JsonValidationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class JsonValidationExtension extends ConfigurableExtension
{
    public function loadInternal(array $config, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator([
            __DIR__ . '/../Resources/config/'
        ]));

        $loader->load('services.xml');

        if ($config['enable_request_listener']) {
            $container->getDefinition('json_validation.request_listener')
                      ->addTag('kernel.event_listener', ['event' => 'kernel.controller', 'priority' => -100])
                      ->addTag('kernel.event_listener', ['event' => 'kernel.controller_arguments', 'priority' => -100])
            ;
        }

        if ($config['enable_response_listener']) {
            $container->getDefinition('json_validation.response_listener')
                      ->addTag('kernel.event_listener', ['event' => 'kernel.response', 'priority' => -100]);
        }

        if ($config['enable_exception_listener']) {
            $container->getDefinition('json_validation.exception_listener')
                      ->addTag('kernel.event_listener', ['event' => 'kernel.exception']);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getAlias(): string
    {
        return 'json_validation';
    }
}
