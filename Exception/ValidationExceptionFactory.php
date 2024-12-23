<?php

namespace Mrsuh\JsonValidationBundle\Exception;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

interface ValidationExceptionFactory
{
    public function createException(Request $request, array $validationConfig, array $validationErrors): \Exception;
}
