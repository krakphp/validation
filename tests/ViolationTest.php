<?php

namespace Krak\Tests;

use Krak\Validation\Violation;

class ViolationTest extends TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(Violation::class, new Violation('code', []));
    }
}
