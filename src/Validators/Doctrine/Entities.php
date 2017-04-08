<?php

namespace Krak\Validation\Validators\Doctrine;

use Krak\Validation;

class Entities extends AbstractDoctrineValidator
{
    public function validate($value, array $ctx = []) {
        $om = $this->getObjectManager($ctx);
        $entity_name = $this->getModelPrefix($ctx) . $this->getEntityName();
        $repo = $om->getRepository($entity_name);
        $entities = $repo->findBy([
            $this->getField() => $values,
        ]);

        if (count($entities) == count($values)) {
            return;
        }


        return Validation\violate(
            'doctrine_entities',
            ['entity_name' => $this->getEntityName(), 'field' => $this->getField()]
        );
    }
}
