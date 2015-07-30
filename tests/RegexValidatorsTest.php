<?php

namespace Krak\Tests;

use Krak\Validation as v;

class RegexValidatorsTest extends TestCase
{
    public function testMatchValidator()
    {
        $v = v\re_match('/ab?c/');
        $this->assertNull($v('ac'));
    }

    public function testMatchValidatorFail()
    {
        $v = v\re_match('/ab?c/');
        $violation = $v('adc');
        $this->assertEquals(V\ViolationCodes::RE_MATCH, $violation->code);
    }

    public function testExcludeValidator()
    {
        $v = v\re_exclude('/ab?c/');
        $this->assertNull($v('def'));
    }

    public function testExcludeValidatorFail()
    {
        $v = v\re_exclude('/ab?c/');
        $violation = $v('abc');
        $this->assertEquals(V\ViolationCodes::RE_EXCLUDE, $violation->code);
    }
}
