<?php

declare(strict_types=1);

namespace Mrsuh\JsonValidationBundle\CompilerPass;

use Mrsuh\JsonValidationBundle\Annotation\ValidateJsonRequest;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\Annotation\Route;

class ValidationConfigurationCompilerPass implements CompilerPassInterface
{
    private const REQUEST_LISTENER_SERVICE_ID = 'json_validation.request_listener';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(self::REQUEST_LISTENER_SERVICE_ID)) {
            return;
        }

        $validationConfig = [];

        foreach (array_keys($container->findTaggedServiceIds('controller.service_arguments')) as $serviceId) {
            $controllerClass = $container->getDefinition($serviceId)->getClass();
            $reflClass = new \ReflectionClass($controllerClass);

            foreach ($reflClass->getMethods(\ReflectionMethod::IS_PUBLIC | ~\ReflectionMethod::IS_STATIC) as $method) {
                $reflMethod = new \ReflectionMethod($controllerClass, $method->getName());
                $routeAttributes = $reflMethod->getAttributes(Route::class);
                if (\count($routeAttributes) < 1) {
                    continue;
                }
                $validateAttributes = $reflMethod->getAttributes(ValidateJsonRequest::class);
                if (\count($validateAttributes) < 1) {
                    continue;
                }

                /** @var Route $routeAttribute */
                $routeAttribute = $routeAttributes[0]->newInstance();
                /** @var ValidateJsonRequest $validateAttribute */
                $validateAttribute = $validateAttributes[0]->newInstance();

                if (!$validateAttribute->isQueryParamsIncluded() && !in_array('POST', $routeAttribute->getMethods(), true)) {
                    $s = 'The controller action "%s::%s" is configured to be validated without including query parameters ' .
                         'in #[%s] but the Route is not a POST route. This will never validate anything! Set ' .
                         'queryParamsIncluded to true if you want to validate against query parameters or include the ' .
                         'POST method.';
                    trigger_error(sprintf($s, $controllerClass, $reflMethod->getName(), ValidateJsonRequest::class), E_USER_WARNING);
                }

                $classMapKey = sprintf('%s::%s', $controllerClass, $reflMethod->getName());
                $config = [
                    'methods' => $validateAttribute->getMethods(),
                    'emptyIsValid' => $validateAttribute->getEmptyIsValid(),
                    'path' => $validateAttribute->getPath(),
                    'queryParamsIncluded' => $validateAttribute->isQueryParamsIncluded(),
                ];
                if (is_string($validateAttribute->getClassString())) {
                    $config['argumentToReplace'] = $this->findArgumentToReplace($reflMethod, $validateAttribute);
                }

                $validationConfig[$classMapKey] = $config;
            }
        }

        if (!empty($validationConfig)) {
            $definition = $container->getDefinition(self::REQUEST_LISTENER_SERVICE_ID);
            $definition->setArgument('$validationConfig', $validationConfig);
        }
    }

    private function findArgumentToReplace(\ReflectionMethod $reflectionMethod, ValidateJsonRequest $validateAttribute): ?string
    {
        $argument = null;
        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            if ($reflectionParameter->getType() instanceof \ReflectionNamedType && $reflectionParameter->getType()->getName() === $validateAttribute->getClassString()) {
                $argument = $reflectionParameter->getName();
                break;
            }
        }

        return $argument;
    }
}