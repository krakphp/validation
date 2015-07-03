<?php

namespace Krak\Tests;

use Krak\Validation as v;

class TypeValidatorsTest extends TestCase
{
    public function testTypeValidatorValid()
    {
        $validator = v\type('bool', 'is_bool');

        $this->assertNull($validator(false));
    }

    public function testTypeValidatorInvalid()
    {
        $validator = v\type('bool', 'is_bool');

        $violation = $validator(1);
        $valid = $violation instanceof V\Violation && $violation->code == 'invalid_type';

        $this->assertTrue($valid);
    }

    /**
     * @dataProvider typeValidatorProvider
     */
    public function testTypeValidator($validator_factory, $val, $fail = false)
    {
        $validator = $validator_factory();

        if ($fail) {
            $this->assertInstanceOf(V\Violation::class, $validator($val));
        }
        else {
            $this->assertNull($validator($val));
        }
    }

    public function typeValidatorProvider()
    {
        $test_instanceof = function() {
            return v\is_instanceof('StdClass');
        };

        return [
            ['krak\validation\type_bool', true],
            ['krak\validation\type_string', ''],
            ['krak\validation\type_numeric', 12.2],
            ['krak\validation\type_int', 0],
            ['krak\validation\type_double', 0.2],
            ['krak\validation\type_array', []],
            ['krak\validation\type_null', null],
            ['krak\validation\is_traversable', new \ArrayObject()],
            ['krak\validation\is_traversable', [], true],
            ['krak\validation\is_iterable', []],
            ['krak\validation\is_iterable', true, true],
            [$test_instanceof, new \StdClass()],
            [$test_instanceof, new \ArrayObject(), true],
        ];
    }
}
