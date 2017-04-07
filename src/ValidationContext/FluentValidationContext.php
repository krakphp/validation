<?php

namespace Krak\Validation\ValidationContext;

use Krak\Validation;
use Krak\Invoke;

class FluentValidationContext implements Validation\ValidationContext
{
    private $invoke;
    private $builder;

    public function __construct(Invoke\Invoke $invoke, FluentValidationBuilder $builder) {
        $this->invoke = $invoke;
        $this->builder = $builder;
    }

    public function validate($value, $validator, array $ctx = []) {
        if (is_string($validator)) {
            $validator = $this->builder->buildValidator($validator);
        } else if (is_array($validator)) {
            $validator = isset($validator[0]) ? Validation\pipe(...$validator) : Validation\collection($validator);
        }

        return $this->invoke->invoke($validator, $value, $ctx);
    }
}
