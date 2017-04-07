<?php

namespace Krak\Validation;

interface Validator {
    public function validate($value, array $ctx = []);
}
