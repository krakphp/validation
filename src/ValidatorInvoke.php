<?php

namespace Krak\Validation;

use Krak\Invoke;

class ValidatorInvoke extends Invoke\InvokeDecorator
{
    public function invoke($func, ...$params) {
        if (!$func instanceof Validator) {
            return $this->invoke->invoke($func, ...$params);
        }

        return $this->invoke->invoke([$func, 'validate'], ...$params);
    }
}
