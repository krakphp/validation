<?php

namespace Krak\Validation\ValidationPackage;

use Krak\Validation\ValidationPackage;
use Krak\Validation;

use function Krak\Validation\createWrapped;

class CoreValidationPackage implements ValidationPackage
{
    public function withValidation(Validation\Kernel $v) {
        $p = Validation\Validators::class . '\\';
        $v->validators([
            'exists' => $p.'exists',
            'required' => $p.'required',
            'optional' => $p.'optional',
            'between' => $p.'between',
            'length' => $p.'length',
            'nullable' => $p.'nullable',
            'string' => $p.'typeString',
            'integer' => $p.'typeInteger',
            'boolean' => $p.'typeBoolean',
            'array' => $p.'typeArray',
            'numeric' => $p.'typeNumeric',
            'null' => $p.'typeNull',
            'float' => $p.'typeFloat',
            'double' => $p.'typeDouble',
            'digits' => $p.'digits',

            'in' => $p.'inArray',
            'all' => $p.'forAll',

            'regex' => $p.'regexMatch',
            'email' => $p.'regexEmail',
            'regex_exclude' => $p.'regexExclude',
        ]);

        $v->messages([
            'exists' => 'The {{attribute}} does not exist.',
            'required' => 'The {{attribute}} is required.',
            'between' => 'The {{attribute}} must be between {{min}} and {{max}}',
            'length' => 'The {{attribute}} cannot be greater than {{max}}',
            'string' => 'The {{attribute}} is not a valid string.',
            'integer' => 'The {{attribute}} is not a valid integer.',
            'boolean' => 'The {{attribute}} is not a valid boolean.',
            'array' => 'The {{attribute}} is not a valid array.',
            'numeric' => 'The {{attribute}} is not a numeric value.',
            'null' => 'The {{attribute}} is not null.',
            'float' => 'The {{attribute}} is not a valid float.',
            'double' => 'The {{attribute}} is not a valid double.',
            'digits' => 'The {{attribute}} must only contain digits.',
            'in' => 'The {{attribute}} is must be one of: {{accepted}}.',
            'regex' => 'The {{attribute}} does not match the pattern: {{pattern}}.',
            'email' => 'The {{attribute}} is not a valid email.',
            'regex_exclude' => 'The {{attribute}} must not match the pattern: {{pattern}}',
        ]);
    }
}
