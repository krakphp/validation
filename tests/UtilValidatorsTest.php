<?php

namespace Krak\Tests;

use Krak\Validation as v;

class UtilValidatorsTest extends TestCase
{
    public function testValidateValidator()
    {
        $this->assertNull(v\validate(new StubValidator(), ''));
    }
    public function testValidateClosure()
    {
        $this->assertNull(v\validate(function($val){}, ''));
    }
    public function testValidateCallable()
    {
        $this->assertNull(v\validate([new StubValidator(), 'stubValidate'], ''));
    }

    public function testValidateException()
    {
        try {
            v\validate(null, '');
            $this->assertTrue(false);
        } catch (V\InvalidValidatorException $e) {
            $this->assertTrue(true);
        }
    }

    public function testMock()
    {
        $v = v\mock('abc');
        $this->assertEquals('abc', $v(''));
    }

    public function testPipeValid()
    {
        $v = v\pipe([v\mock(null)]);
        $this->assertNull($v(''));
    }
    public function testPipeInvalidGreedy()
    {
        $v = v\pipeg([v\mock(true)]);
        $this->assertInternalType('array', $v(''));
    }
    public function testPipeInvalidNonGreedy()
    {
        $v = v\pipe([v\mock(true)]);
        $this->assertTrue($v(''));
    }

    public function testCollectionValid()
    {
        $v = v\collection(['key'=>v\mock(null)]);
        $this->assertNull($v(['key'=>'']));
    }
    public function testCollectionErrOnMissing()
    {
        $v = v\collection(['key' => v\mock(null)]);
        $this->assertCount(1, $v([]));
    }
    public function testCollectionErrOnExtra()
    {
        $v = v\collection([], true);
        $this->assertCount(1, $v(['key' => '']));
    }
    public function testCollectionInvalid()
    {
        $v = v\collection(['a'=>v\mock(true)]);
        $this->assertEquals(
            ['a' => true],
            $v(['a' => ''])
        );
    }
    public function testCollectionOptional()
    {
        $v = v\collection(['a'=> ['o', v\mock(true)]]);
        $this->assertNull($v([]));
    }

    public function testStub()
    {
        $v = v\stub();
        $this->assertNull($v(''));
    }
    public function testExists()
    {
        $v = v\exists();
        $this->assertNull($v(''));
    }
    public function testChoice()
    {
        $v = v\choice(['a', 'b']);
        $this->assertNull($v('a'));
    }
    public function testChoiceInvalid()
    {
        $v = v\choice([]);
        $this->assertInstanceOf(V\Violation::class, $v('a'));
    }
    public function testTransform()
    {
        $v = v\transform(function($val) {
            return $val + 1;
        }, function($val)
        {
            return $val == 2 ? null : new V\Violation('err', []);
        });
        $this->assertNull($v(1));
    }

    public function testForAllInvalid()
    {
        $v = v\for_all(v\mock(1));
        $this->assertInstanceOf(V\Violation::class, $v([1]));
    }

    public function testForAll()
    {
        $v = v\for_all(v\stub());
        $this->assertNull($v([]));
    }
}

class StubValidator implements V\Validator
{
    public function validateValue($value)
    {
        return null;
    }

    public function stubValidate($value)
    {
        return null;
    }
}
