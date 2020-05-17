<?php

namespace Tests;

use Mrsuh\JsonValidationBundle\Annotation\ValidateJsonRequest;
use PHPUnit\Framework\TestCase;

class ValidateJsonTest extends TestCase
{
    /**
     * @dataProvider constructorOptionsProvider
     */
    public function testConstructorOptions(array $options, string $expectedPath, bool $expectedEmptyIsValid, array $expectedMethods)
    {
        $annotation = new ValidateJsonRequest($options);

        $this->assertEquals($annotation->getPath(), $expectedPath);
        $this->assertEquals($annotation->getEmptyIsValid(), $expectedEmptyIsValid);
        $this->assertEquals($annotation->getMethods(), $expectedMethods);
    }

    public function testInvalidConstructorOptions()
    {
        $this->expectException(\RuntimeException::class);
        new ValidateJsonRequest(['invalid_option' => 'yes']);
    }

    public function constructorOptionsProvider(): array
    {
        return [
            [['value' => 'abc'], 'abc', false, []],
            [['path' => 'abc'], 'abc', false, []],
            [['path' => 'abc', 'emptyIsValid' => true], 'abc', true, []],
            [['path' => 'abc', 'methods' => ['POST', 'PUT']], 'abc', false, ['POST', 'PUT']],
        ];
    }
}
