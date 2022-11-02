<?php

namespace Mrsuh\JsonValidationBundle;

use Mrsuh\JsonValidationBundle\CompilerPass\ValidationConfigurationCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Mrsuh\JsonValidationBundle\DependencyInjection\JsonValidationExtension;

class JsonValidationBundle extends Bundle
{
    protected function createContainerExtension(): ?ExtensionInterface
    {
        return new JsonValidationExtension();
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new ValidationConfigurationCompilerPass());
    }
}
