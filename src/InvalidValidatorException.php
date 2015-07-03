<?php

namespace Krak\Validation;

use RuntimeException;

class InvalidValidatorException extends RuntimeException
{
    public function __construct() {
        $msg = 'Validator must be an instance of Krak\Validation\Validator, ' .
            'Closure, or a callable';
        parent::__construct($msg);
    }
}
