<?php

namespace Krak\Validation\ValidationPackage;

use Krak\Validation;
use Krak\Validation\Validators\Doctrine;

class DoctrineValidationPackage implements Validation\ValidationPackage
{
    public function withValidation(Validation\Kernel $v) {
        $v->validators([
            'doctrine_all_exist' => Doctrine\AllExist::class,
            'doctrine_entities' => Doctrine\Entities::class,
            'doctrine_entity' => Doctrine\Entity::class,
            'doctrine_exists' => Doctrine\Exists::class,
            'doctrine_unique' => Doctrine\Unique::class,
            'doctrine_unique_entity' => Doctrine\UniqueEntity::class,
        ]);
        $v->messages([
            'doctrine_all_exist' => 'The {{attribute}} do not exist.',
            'doctrine_entities' => 'The {{attribute}} do not exist.',
            'doctrine_entity' => 'The {{attribute}} does not exist.',
            'doctrine_exists' => 'The {{attribute}} does not exist.',
            'doctrine_unique' => 'The {{attribute}} is not unique.',
            'doctrine_unique_entity' => 'The {{attribute}} is not unique.',
        ]);
    }
}
