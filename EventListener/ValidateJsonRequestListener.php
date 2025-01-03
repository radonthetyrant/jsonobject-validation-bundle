<?php

namespace Mrsuh\JsonValidationBundle\EventListener;

use JsonSchema\Constraints\Constraint;
use Mrsuh\JsonValidationBundle\Exception\JsonValidationRequestException;
use Mrsuh\JsonValidationBundle\Exception\ValidationExceptionFactory;
use Mrsuh\JsonValidationBundle\JsonValidator\JsonValidator;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ValidateJsonRequestListener
{
    public function __construct(
        protected JsonValidator $jsonValidator,
        protected ValidationExceptionFactory $validationExceptionFactory,
        protected DenormalizerInterface $denormalizer,
        protected array $validationConfig = [],
    )
    {
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $request = $event->getRequest();

        if (empty($this->validationConfig)) {
            return;
        }

        if (!$request->attributes->has('_controller') || null === ($validationConfig = $this->validationConfig[$request->attributes->get('_controller')] ?? null)) {
            return;
        }

        $httpMethods = array_map(function (string $method): string {
            return strtoupper($method);
        }, $validationConfig['methods']);

        if (!empty($httpMethods) && !in_array($request->getMethod(), $httpMethods)) {
            return;
        }

        $content = $request->attributes->get('_route_params') ?? [];
        if ($request->isMethod('POST') || $request->isMethod('PATCH') || $request->isMethod('DELETE') || $request->isMethod('PUT')) {
            try {
                $content = array_merge($content, $request->toArray());
            } catch (JsonException $e) {
                if ($e->getMessage() === 'Request body is empty.' && !$validationConfig['emptyIsValid']) {
                    throw $e;
                }
            }
        }

        if ($request->isMethod('GET') || $validationConfig['queryParamsIncluded']) {
            $content = array_merge($content, $request->request->all(), $request->query->all());
        }

        $objectData = $this->jsonValidator->validate(
            $content,
            $validationConfig['path'],
        );

        if (!empty($this->jsonValidator->getErrors())) {
            throw $this->validationExceptionFactory->createException($request, $validationConfig, $this->jsonValidator->getErrors());
        }

        $replacedObject = $this->denormalizer->denormalize($objectData, $validationConfig['argumentClassString'], 'request', ['request' => $request]);
        $request->attributes->set('_replace_arguments', [$validationConfig['argumentToReplace'] => $replacedObject]);
    }

    public function onKernelControllerArguments(ControllerArgumentsEvent $event): void
    {
    }
}
