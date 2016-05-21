<?php

namespace Krak\Tests;

use Krak\Validation\Violation;
use function Krak\Validation\violate,
    Krak\Validation\find_violation,
    Krak\Validation\type_string,
    Krak\Validation\collection;

class ViolationTest extends TestCase
{
    public function testConstruct() {
        $this->assertInstanceOf(Violation::class, new Violation('code', []));
    }

    public function testViolate() {
        $this->assertInstanceOf(Violation::class, violate('code'));
    }

    /** @dataProvider findValidationProvider */
    public function testFindValidation($expected, $violation, $code, $path) {
        // var_dump($expected);var_dump($violation);var_dump($code);
        $violation = find_violation($violation, $code, $path);

        $valid = $expected == true
            ? !is_null($violation) && $violation->code == $code
            : is_null($violation);

        $this->assertTrue($valid);
    }

    public function findValidationProvider() {
        $s = type_string();
        $c = collection([
            'a' => $s
        ]);

        return [
            [false, $s(''), 'invalid_type', ''],
            [true, $s(1), 'invalid_type', ''],
            [false, $s(''), 'invalid_type', 'key'],
            [false, $s(''), 'bad_code', ''],
            [true, $c([]), 'missing_field', 'a'],
            [true, $c(['a' => 1]), 'invalid_type', 'a'],
            [false, $c(['a' => 1]), 'invalid_type', 'b'],
        ];
    }
}
