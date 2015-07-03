<?php

namespace Krak\Tests;

use Krak\Validation as v,
    Phake,
    Symfony\Component\Validator;

class SymfonyValidatorsTest extends TestCase
{
    public function getValidator()
    {
        return Phake::mock(Validator\Validator\ValidatorInterface::class);
    }

    public function testSymfony()
    {
        $validator = $this->getValidator();
        Phake::when($validator)->validate->thenReturn([1]);
        $factory = v\symfony_factory($validator);

        $v = $factory(new Validator\Constraints\IsNull());
        $this->assertInstanceOf(V\Violation::class, $v(1));
    }
}
