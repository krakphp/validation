<?php

namespace Krak\Tests;

use Doctrine\Common\Persistence,
    Krak\Validation as v,
    Phake;

class DoctrineValidatorsTest extends TestCase
{
    public function testDoctrineEntity()
    {
        $om = Phake::mock(Persistence\ObjectManager::class);
        Phake::when($om)->find()->thenReturn(null);

        $v = v\doctrine_entity($om, 'Class');
        $this->assertInstanceOf(V\Violation::class, $v(1));
    }

    public function testDoctrineUniqueEntity()
    {
        $or = Phake::mock(Persistence\ObjectRepository::class);
        Phake::when($or)->findOneBy->thenReturn(1);

        $v = v\doctrine_unique_entity($or, 'field');
        $this->assertInstanceOf(V\Violation::class, $v(1));
    }
}
