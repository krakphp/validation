<?php

namespace Krak\Validation\ValidationContext;

use Krak\Validation;

class FluentValidationBuilder
{
    private $validators;

    public function __construct($validators) {
        $this->validators = $validators;
    }

    public function buildValidator($validator) {
        $parts = explode('|', $validator);
        $validators = array_map(function($part) {
            $tup = explode(':', $part);
            if (count($tup) > 1) {
                list($validator, $args) = $tup;
                $args = explode(',', $args);
            } else {
                $validator = $tup[0];
                $args = [];
            }

            if (isset($this->validators[$validator])) {
                $validator = $this->validators[$validator](...$args);
            }

            return $validator;
        }, $parts);

        return count($validators) > 1 ? Validation\pipe(...$validators) : $validators[0];
    }
}
