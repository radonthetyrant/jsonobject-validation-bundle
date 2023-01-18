<?php

namespace Mrsuh\JsonValidationBundle\EventListener;

use JsonSchema\Constraints\Constraint;
use Mrsuh\JsonValidationBundle\Annotation\ValidateJsonRequest;
use Mrsuh\JsonValidationBundle\Exception\JsonValidationRequestException;
use Mrsuh\JsonValidationBundle\JsonValidator\JsonValidator;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ValidateJsonRequestListener
{
    public function __construct(
        protected JsonValidator $jsonValidator,
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
        if ($request->isMethod('POST') || $request->isMethod('PATCH')) {
            $content = $request->toArray();
        }

        if ($request->isMethod('GET') || $validationConfig['queryParamsIncluded']) {
            $content = array_merge($content, $request->query->all());
        }

        $objectData = $this->jsonValidator->validate(
            $content,
            $validationConfig['path'],
            Constraint::CHECK_MODE_TYPE_CAST
        );

        if (!empty($this->jsonValidator->getErrors())) {
            throw new JsonValidationRequestException($request, $validationConfig['path'], $this->jsonValidator->getErrors());
        }

        $replacedObject = $this->denormalizer->denormalize($objectData, $validationConfig['argumentClassString'], 'request', ['request' => $request]);
        $request->attributes->set('_replace_arguments', [$validationConfig['argumentToReplace'] => $replacedObject]);
    }

    public function onKernelControllerArguments(ControllerArgumentsEvent $event): void
    {
    }
}
