<?php

namespace Mrsuh\JsonValidationBundle\JsonValidator;

use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Errors\ValidationError;
use Opis\JsonSchema\Validator;

class OpisValidator extends JsonValidator
{
    public function validate(string|array|object $json, string $schemaPath)
    {
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

        if ($json === null) {
            $this->errors[] = [
                'property'   => null,
                'pointer'    => null,
                'message'    => '[' . json_last_error() . '] ' . json_last_error_msg(),
                'constraint' => null,
            ];

            return null;
        }

        $validator = new Validator();

        $result = $validator->validate(
            $json,
            file_get_contents($schema),
        );

        if ($result->hasError()) {
            /** @var ValidationError $err */
            $err = $result->error();
            $formatter = new ErrorFormatter();

            $this->errors[] = [
                'property'   => null,
                'pointer'    => null,
                'message'    => $formatter->formatErrorMessage($err),
                'constraint' => null,
            ];

        }

        return $json;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}