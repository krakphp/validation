<?php

namespace Krak\Tests;

use Doctrine\Common\Persistence\ObjectRepository,
    Krak\Validation as v,
    Phake;

class DoctrineValidatorsTest extends TestCase
{
    public function testDoctrineEntity()
    {
        $repo = Phake::mock(ObjectRepository::class);
        Phake::when($repo)->findOneBy->thenReturn(null);

        $v = v\doctrine_entity($repo, 'id', 'alias');
        $this->assertInstanceOf(V\Violation::class, $v(1));
    }

    public function testDoctrineEntities()
    {
        $repo = Phake::mock(ObjectRepository::class);
        Phake::when($repo)->findBy->thenReturn([]);

        $v = v\doctrine_entities($repo, 'id', 'alias');
        $this->assertInstanceOf(V\Violation::class, $v([1]));
    }

    public function testDoctrineUniqueEntity()
    {
        $or = Phake::mock(ObjectRepository::class);
        Phake::when($or)->findOneBy->thenReturn(1);

        $v = v\doctrine_unique_entity($or, 'field');
        $this->assertInstanceOf(V\Violation::class, $v(1));
    }
}
