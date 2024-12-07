<?php

declare(strict_types=1);

namespace Mrsuh\JsonValidationBundle\ArgumentResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class DenormalizedJsonArgumentValueResolver implements ValueResolverInterface
{
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!$request->attributes->has('_replace_arguments')) {
            return [];
        }

        yield from $request->attributes->get('_replace_arguments');
    }
}
