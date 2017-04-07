<?php

namespace Krak\Validation\ValidationPackage;

use Krak\Validation\ValidationPackage;
use Krak\Validation;

use function Krak\Validation\createWrapped;

class CoreValidationPackage implements ValidationPackage
{
    public function withValidation(Validation\Kernel $v) {
        $v->validators([
            'exists' => 'Krak\Validation\exists',
            'optional' => 'Krak\Validation\optional',
            'between' => 'Krak\Validation\between',
            'length' => 'Krak\Validation\length',
            'nullable' => 'Krak\Validation\nullable',
            'string' => createWrapped('is_string', 'string'),
            'integer' => createWrapped('is_int', 'integer'),
            'array' => createWrapped('is_array', 'array'),
            'numeric' => createWrapped('is_numeric', 'numeric'),
            'null' => createWrapped('is_null', 'null'),
            'float' => createWrapped('is_float', 'float'),
            'digits' => createWrapped('ctype_digit', 'digits'),
        ]);
    }
}
