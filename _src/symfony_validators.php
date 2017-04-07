<?php

namespace Krak\Validation;

use Symfony\Component\Validator\Validator\ValidatorInterface;

function symfony_factory(ValidatorInterface $validator)
{
    return function($constraints) use ($validator) {
        return symfony($validator, $constraints);
    };
}

function symfony(ValidatorInterface $validator, $constraints)
{
    return function($value) use ($validator, $constraints) {
        $violations = $validator->validate($value, $constraints);
        if (count($violations)) {
            return violate(ViolationCodes::FAILED_SYMFONY, null);
        }
    };
}
