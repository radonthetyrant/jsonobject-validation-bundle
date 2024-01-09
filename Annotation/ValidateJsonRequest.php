<?php

namespace Mrsuh\JsonValidationBundle\Annotation;

#[\Attribute(\Attribute::TARGET_METHOD)]
class ValidateJsonRequest
{
    const ALIAS = 'validate_json_request';

    /**
     * @param null|class-string $classString
     */
    public function __construct(
        private string $path,
        private bool $emptyIsValid = false,
        private array $methods = [],
        private ?string $classString = null,
        private bool $queryParamsIncluded = false,
    )
    {
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setEmptyIsValid(bool $emptyIsValid): void
    {
        $this->emptyIsValid = $emptyIsValid;
    }

    public function getEmptyIsValid(): bool
    {
        return $this->emptyIsValid;
    }

    public function setMethods($methods): void
    {
        if (is_string($methods)) {
            $methods = [$methods];
        }

        $this->methods = $methods;
    }

    public function getMethods(): array
    {
        return $this->methods;
    }

    public function getClassString(): ?string
    {
        return $this->classString;
    }

    public function setClassString(?string $classString): void
    {
        $this->classString = $classString;
    }

    public function isQueryParamsIncluded(): bool
    {
        return $this->queryParamsIncluded;
    }

    public function setQueryParamsIncluded(bool $queryParamsIncluded): void
    {
        $this->queryParamsIncluded = $queryParamsIncluded;
    }

    /**
     * {@inheritDoc}
     */
    public function getAliasName(): string
    {
        return self::ALIAS;
    }

    /**
     * {@inheritDoc}
     */
    public function allowArray(): bool
    {
        return false;
    }
}
