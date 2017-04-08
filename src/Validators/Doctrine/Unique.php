<?php

namespace Krak\Validation\Validators\Doctrine;

use Krak\Validation;

class Unique extends AbstractDoctrineValidator
{
    private $validator;

    public function __construct($table_name, $field = 'id') {
        parent::__construct($table_name, $field);
        $this->validator = new Exists($table_name, $field);
    }

    public function validate($value, array $ctx = []) {
        $v = $this->validator->validate($value, $ctx);

        if ($v) {
            return;
        }

        return Validation\violate(
            'doctrine_unique',
            ['table_name' => $this->getTableName(), 'field' => $this->getField()]
        );
    }
}
