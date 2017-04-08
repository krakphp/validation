<?php

namespace Krak\Validation\Validators\Doctrine;

use Krak\Validation;

class Exists extends AbstractDoctrineValidator
{
    public function validate($value, array $ctx = []) {
        $conn = $this->getConnection($ctx);
        $stmt = $conn->prepare(sprintf('SELECT COUNT(*) FROM %s WHERE %s = :value', $this->getTableName(), $this->getField()));
        $stmt->bindValue('value', $value);
        $stmt->execute();
        $count = (int) $stmt->fetchColumn(0);

        if ($count > 0) {
            return;
        }

        return Validation\violate('doctrine_exists', [
            'table_name' => $this->getTableName(),
            'field' => $this->getField(),
        ]);
    }
}
