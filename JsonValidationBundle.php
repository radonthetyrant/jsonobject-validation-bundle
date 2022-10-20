<?php

namespace Mrsuh\JsonValidationBundle;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Mrsuh\JsonValidationBundle\DependencyInjection\JsonValidationExtension;

class JsonValidationBundle extends Bundle
{
    protected function createContainerExtension(): ?ExtensionInterface
    {
        return new JsonValidationExtension();
    }
}
