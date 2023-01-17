<?php

declare(strict_types=1);

namespace Mrsuh\JsonValidationBundle\ArgumentResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class DenormalizedJsonArgumentValueResolver implements ArgumentValueResolverInterface
{

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return $request->attributes->has('_replace_arguments');
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        yield from $request->attributes->get('_replace_arguments');
    }
}
