<?php

declare(strict_types=1);

namespace Mrsuh\JsonValidationBundle\Enum;

enum DataSource: int
{
    case Query = 1;
    case Body = 2;
}