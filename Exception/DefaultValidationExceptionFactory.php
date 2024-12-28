<?php

namespace Mrsuh\JsonValidationBundle\Exception;

use JsonSchema\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class DefaultValidationExceptionFactory implements ValidationExceptionFactory
{
    /**
     * @param Request $request
     * @param array{methods: string[], path: string, queryParamsIncluded?: bool, emptyIsValid?: bool, argumentToReplace: string, argumentClassString: class-string} $validationConfig
     * @param array   $validationErrors
     *
     * @return HttpExceptionInterface
     */
    public function createException(Request $request, array $validationConfig, array $validationErrors): \Exception
    {
        if (count($validationErrors) > 0) {
            $validationError = array_shift($validationErrors);
            return new ValidationException(sprintf('Error validating %s: %s', $validationError['pointer'], $validationError['message']));
        }
        return new JsonValidationRequestException($request, $validationConfig['path'], $validationErrors);
    }
}
