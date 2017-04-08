<?php

namespace Krak\Validation\Validators\Doctrine;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\Connection;
use Krak\Validation;

abstract class AbstractDoctrineValidator implements Validation\Validator
{
    protected $name;
    protected $field;

    public function __construct($name, $field = 'id') {
        $this->name = $name;
        $this->field = $field;
    }

    abstract public function validate($value, array $ctx = []);

    protected function getEntityName() {
        return $this->name;
    }

    protected function getTableName() {
        return $this->name;
    }

    protected function getField() {
        return $this->field;
    }

    protected function getObjectManager(array $ctx) {
        $key = Validation\contextGet($ctx, 'doctrine.om_key', ObjectManager::class);
        return Validation\contextContainer($ctx, $key);
    }

    protected function getModelPrefix(array $ctx) {
        $prefix = Validation\contextGet($ctx, 'doctrine.model_prefix');
        if (!$prefix) {
            return '';
        }

        return rtrim($prefix, '\\') . '\\';
    }

    protected function getConnection(array $ctx) {
        $key = Validation\contextGet($ctx, 'doctrine.connection_key', Connection::class);
        return Validation\contextContainer($ctx, $key);
    }
}
