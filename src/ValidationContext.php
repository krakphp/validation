<?php

namespace Krak\Validation;

interface ValidationContext {
    public function validate($value, $validator, array $ctx = []);
}
