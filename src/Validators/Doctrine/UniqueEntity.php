<?php

namespace Krak\Validation\Validators\Doctrine;

use Krak\Validation;

class UniqueEntity extends AbstractDoctrineValidator
{
    private $validator;

    public function __construct($entity_name, $field = 'id') {
        parent::__construct($entity_name, $field);
        $this->validator = new Entity($entity_name, $field);
    }

    public function validate($value, array $ctx = []) {
        $v = $this->validator->validate($value, $ctx);

        if ($v) {
            return;
        }

        return Validation\violate(
            'doctrine_unique_entity',
            ['entity_name' => $this->getEntityName(), 'field' => $this->getField()]
        );
    }
}
