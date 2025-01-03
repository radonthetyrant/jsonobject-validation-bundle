<?php

namespace Mrsuh\JsonValidationBundle\JsonValidator;

use JsonSchema\Constraints\Constraint;
use JsonSchema\Exception\JsonDecodingException;
use JsonSchema\Validator;
use Symfony\Component\Config\FileLocatorInterface;

class JsonValidator
{
    /** @var FileLocatorInterface */
    protected $locator;

    protected $schemaDir = '';

    /** @var array */
    protected $errors = [];

    public function __construct(FileLocatorInterface $locator, string $schemaDir)
    {
        $this->locator   = $locator;
        $this->schemaDir = $schemaDir;
    }

    public function validate(string|array|object $json, string $schemaPath)
    {
        $this->errors = [];
        $schema       = null;

        try {
            $schema = $this->locator->locate(rtrim($this->schemaDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $schemaPath);
        } catch (\InvalidArgumentException $e) {
            $this->errors[] = [
                'property'   => null,
                'pointer'    => null,
                'message'    => 'Unable to locate schema ' . $schemaPath,
                'constraint' => null,
            ];

            return null;
        }

        if (is_string($json)) {
            $data = json_decode($json);
        } else {
            $data = $json;
        }

        if ($data === null) {
            $this->errors[] = [
                'property'   => null,
                'pointer'    => null,
                'message'    => '[' . json_last_error() . '] ' . json_last_error_msg(),
                'constraint' => null,
            ];

            return null;
        }

        $validator = new Validator();

        try {
            $validator->validate(
                $data,
                (object)['$ref' => 'file://' . $schema],
                Constraint::CHECK_MODE_TYPE_CAST
            );
        } catch (JsonDecodingException $e) {
            $this->errors[] = [
                'property'   => null,
                'pointer'    => null,
                'message'    => $e->getMessage(),
                'constraint' => null,
            ];

            return null;
        }

        if (!$validator->isValid()) {
            $this->errors = $validator->getErrors();

            return null;
        }

        return $data;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
