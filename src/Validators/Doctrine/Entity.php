<?php

namespace Krak\Validation\Validators\Doctrine;

use Krak\Validation;

class Entity extends AbstractDoctrineValidator
{
    public function validate($value, array $ctx = []) {
        $om = $this->getObjectManager($ctx);
        $entity_name = $this->getModelPrefix($ctx) . $this->getEntityName();
        $repo = $om->getRepository($entity_name);
        $entity = $repo->findOneBy([
            $this->getField() => $value,
        ]);

        if ($entity) {
            return;
        }

        return Validation\violate('doctrine_entity', [
            'entity_name' => $this->getEntityName(),
            'field' => $this->getField(),
        ]);
    }
}
