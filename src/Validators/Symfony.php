<?php

namespace Krak\Validation\Validators;

use Krak\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Symfony implements Validation\Validator
{
    private $constraints;

    public function __construct($constraints) {
        $this->constraints = $constraints;
    }

    public function validate($value, array $ctx = []) {
        $validator = Validation\contextContainer(
            $ctx,
            Validation\contextGet($ctx, 'symfony.validator_key', ValidatorInterface::class)
        );
        $violations = $validator->validate($value, $constraints);

        if (!count($violations)) {
            return;
        }

        return Validation\violate('symfony', [
            'violations' => $violations,
        ]);
    }
}
