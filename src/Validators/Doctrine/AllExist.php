<?php

namespace Krak\Validation\Validators\Doctrine;

use Krak\Validation;

class AllExist extends AbstractDoctrineValidator
{
    private $type;

    public function __construct($table_name, $field = 'id', $type = 'int') {
        parent::__construct($table_name, $field);
        $this->type = $type;
    }

    public function validate($value, array $ctx = []) {
        $conn = $this->getConnection($ctx);
        $stmt = $conn->executeQuery(
            sprintf('SELECT COUNT(*) FROM %s WHERE %s IN (?)', $this->getTableName(), $this->getField()),
            [$value],
            [$this->type == 'int' ? Connection::PARAM_INT_ARRAY : Connection::PARAM_STR_ARRAY]
        );
        $count = (int) $stmt->fetchColumn(0);

        if ($count == count($value)) {
            return;
        }

        return Validation\violate('doctrine_all_exist', [
            'table_name' => $this->getTableName(),
            'type' => $this->type,
            'field' => $this->getField(),
        ]);
    }
}
