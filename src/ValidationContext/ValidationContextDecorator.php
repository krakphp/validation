<?php

namespace Krak\Validation\ValidationContext;

use Krak\Validation;

abstract class ValidationContextDecorator implements Validation\ValidationContext
{
    protected $validation_context;

    public function __construct(Validation\ValidationContext $validation_context) {
        $this->validation_context = $validation_context;
    }

    abstract public function validate($value, $validator, array $ctx = []); 
}
