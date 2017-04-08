<?php

namespace Krak\Validation\ValidationContext;

use Krak\Validation;
use Krak\Invoke;
use function Krak\Validation\Validators\pipe;
use function Krak\Validation\Validators\collection;

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
            $validator = isset($validator[0]) ? pipe(...$validator) : collection($validator);
        }

        return $this->invoke->invoke($validator, $value, $ctx);
    }
}
