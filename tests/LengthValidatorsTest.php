<?php

namespace Krak\Tests;

use Krak\Validation as v;

class LengthValidatorsTest extends TestCase
{
    private function assertValidator($v, $val, $violation_code)
    {
        $res = $v($val);

        if ($violation_code) {
            $this->assertEquals($violation_code, $res->code);
        }
        else {
            $this->assertNull($res);
        }
    }


    /**
     * @dataProvider lengthValidatorProvider
     */
    public function testLengthValidator($cmp, $val, $violation_code)
    {
        $this->assertValidator(v\length($cmp), $val, $violation_code);
    }

    public function lengthValidatorProvider()
    {
        return [
            'a' => [v\gt(1), 'ab', ''],
            'b' => [v\gt(1), 'a', 'not_greater_than'],
            'c' => [v\lt(2), 'a', ''],
            'd' => [v\lt(2), 'ab', 'not_less_than'],
            'gte_pass' => [v\gte(3), 'abc', ''],
            'gte_fail' => [v\gte(3), 'ab', 'not_greater_than_or_equal'],
            'lte_pass' => [v\lte(3), 'abc', ''],
            'lte_fail' => [v\lte(3), 'abcd', 'not_less_than_or_equal'],
            'i' => [v\eq(3), 'abc', ''],
            'j' => [v\eq(2), 'abc', 'not_equal'],
            'k' => [v\eq(null, false), '', ''],
            'l' => [v\eq(2, false), 'abc', 'not_equal'],
        ];
    }

    /**
     * @dataProvider countValidatorProvider
     */
    public function testCountValidator($cmp, $val, $violation_code)
    {
        $this->assertValidator(v\count($cmp), $val, $violation_code);
    }

    public function countValidatorProvider()
    {
        return [
            'eq_pass' => [v\eq(1), [1], ''],
        ];
    }
}
