<?php

namespace Krak\Validation;

interface Validator
{
    /**
     * @return Violation[]|Violation|null
     */
    public function validateValue($value);
}
