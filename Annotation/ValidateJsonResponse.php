<?php

namespace Mrsuh\JsonValidationBundle\Annotation;

#[\Attribute(\Attribute::TARGET_METHOD)]
class ValidateJsonResponse
{
    const string ALIAS = 'validate_json_response';

    /**
     * The path to the JSON schema
     *
     * @var string
     */
    private string $path = '';

    /**
     * Whether an empty JSON request value is valid
     *
     * @var bool
     */
    private bool $emptyIsValid = false;

    /**
     * Only validate on certain HTTP statuses
     *
     * @var array
     */
    public array $statuses = [];

    /**
     * @param array $data An array of key/value parameters
     * @throws \BadMethodCallException
     * @see Symfony\Component\Routing\Attribute\Route
     */
    public function __construct(array $data)
    {
        if (isset($data['value'])) {
            $data['path'] = $data['value'];
            unset($data['value']);
        }
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

    public function setStatuses($statuses): void
    {
        if (is_string($statuses)) {
            $this->statuses = [$statuses];

            return;
        }

        $this->statuses = $statuses;
    }

    public function getStatuses(): array
    {
        return $this->statuses;
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
