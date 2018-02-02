<?php

namespace Krak\Validation\ValidationContext;

use Krak\Validation;
use function Krak\Validation\Validators\pipe;

class FluentValidationBuilder
{
    private $validators;
    private $aliases;

    public function __construct($validators, $aliases = []) {
        $this->validators = $validators;
        $this->aliases = $aliases;
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

            $validator = $this->resolveAlias($validator);
            if (isset($this->validators[$validator])) {
                $validator_def = $this->validators[$validator];
                if (is_callable($validator_def)) {
                    $validator = $validator_def(...$args);
                } else if (class_exists($validator_def)) {
                    $validator = new $validator_def(...$args);
                } else {
                    throw new \LogicException("Validator '$validator' is not a valid callable or class name.");
                }
            }

            return $validator;
        }, $parts);

        return count($validators) > 1 ? pipe(...$validators) : $validators[0];
    }

    private function resolveAlias($validator, $prev_validators = []) {
        if (in_array($validator, $prev_validators)) {
            throw new Validation\Exception\CircularReferenceException('A circular reference of aliases has been detected: ' . implode(' -> ', $prev_validators + [$validator]));
        }

        return isset($this->aliases[$validator])
            ?  $this->resolveAlias($this->aliases[$validator], $prev_validators + [$validator])
            : $validator;
    }
}
