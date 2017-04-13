<?php

namespace Krak\Validation\Validators;

use Krak\Validation;

use iter;

function collection($validators, $err_on_extra = false) {
    return function($value, array $ctx = []) use ($validators, $err_on_extra) {
        if (!is_array($value)) {
            return Validation\violate('array');
        }

        if ($err_on_extra && count($value) > count($validators)) {
            return Validation\violate('extra_fields');
        }

        $errors = iter\reduce(function($acc, $validator, $key) use ($value, $ctx) {
            $field_key = Validation\contextFieldKey($ctx, $key);
            if (array_key_exists($key, $value)) {
                $res = Validation\validate($value[$key], $validator, array_merge($ctx, [
                    'field_exists' => true,
                    'field_key' => $field_key,
                ]));
            } else {
                $res = Validation\validate(null, $validator, array_merge($ctx, [
                    'field_exists' => false,
                    'field_key' => $field_key
                ]));
            }
            if ($res && !$res->get('valid', false)) {
                $acc[] = $res->with('attribute', $field_key);
            }
            return $acc;
        }, $validators, []);

        if ($errors) {
            return Validation\violations($errors);
        }
    };
}

function pipe(...$validators) {
    return function($value, array $context = []) use ($validators) {
        foreach ($validators as $validator) {
            $res = Validation\validate($value, $validator, $context);

            if ($res) {
                return $res;
            }
        }
    };
}

function pipeAll(...$validators) {
    return function($value, array $context = []) use ($validators) {
        $violations = [];
        foreach ($validators as $validator) {
            $res = Validation\validate($value, $validator, $context);

            if ($res && !$res->get('valid', false)) {
                $violations[] = $res;
            }
        }

        if ($violations) {
            return Validation\violations($errors);
        }
    };
}

function optional() {
    return function($value, array $ctx = []) {
        if (!isset($ctx['field_exists'])) {
            return;
        }

        if (!$ctx['field_exists']) {
            return Validation\violate('optional')->with('valid', true);
        }
    };
}

function nullable() {
    return function($value, array $ctx = []) {
        if (is_null($value)) {
            return Validation\violate('nullable')->with('valid', true);
        }
    };
}

function wrap($name, $func) {
    return function($value) use ($name, $func) {
        if ($func($value)) {
            return;
        }

        return Validation\violate($name);
    };
}

function exists() {
    $validator = required();
    return function($value, array $ctx = []) use ($validator) {
        $v = $validator($value, $ctx);
        if (!$v) {
            return;
        }

        return $v->withCode('exists');
    };
}

function required() {
    return function($value, array $ctx = []) {
        if (!isset($ctx['field_exists'])) {
            return;
        }

        if (!$ctx['field_exists']) {
            return Validation\violate('required');
        }
    };
}

function between($min, $max) {
    $min = (int) $min;
    $max = (int) $max;
    return function($value) use ($min, $max) {
        if (is_string($value)) {
            $value = strlen($value);
        } else if (is_array($value)) {
            $value = count($value);
        }

        return $min <= $value && $value <= $max ? null : Validation\violate('between', [
            'min' => $min,
            'max' => $max
        ]);
    };
}

function length($max) {
    return function($value) use ($max) {
        $v = between($max, $max);
        $res = $v($value);
        return $res ? $res->without('min')->withCode('length') : null;
    };
}

function different(...$fields) {
    return function($data) use ($fields) {
        $value = $data[$fields[0]];
        foreach (array_slice($fields, 1) as $field) {
            if ($value === $data[$field]) {
                return Validation\violate('different', [
                    'fields' => $fields,
                ]);
            }
        }
    };
}

function same(...$fields) {
    return function($data) use ($fields) {
        $value = $data[$fields[0]];
        foreach (array_slice($fields, 1) as $field) {
            if ($value !== $data[$field]) {
                return Validation\violate('same', [
                    'fields' => $fields,
                ]);
            }
        }
    };
}

function fields($fields, $assert) {
    return function($data) use ($fields, $assert) {
        $value = $data[$fields[0]];
        foreach (array_slice($fields, 1) as $field) {
            if (!$assert($value, $data[$field])) {
                return Validation\violate('fields', [
                    'fields' => $fields,
                ]);
            }
        }
    };
}

/** # Array Validators **/

/**
 * Validates that a value is inside of an array of accepted values
 */
function inArray(...$accepted) {
    return function($value) use ($accepted) {
        if (in_array($value, $accepted)) {
            return;
        }

        return Validation\violate(
            'in',
            ['accepted' => $accepted]
        );
    };
}

/**
 * Apply the validator for all values in an array
 */
function forAll($validator) {
    return function($values, array $ctx = []) use ($validator) {
        $violations = iter\map(function($value, $key) use ($validator, $ctx) {
            $v = Validation\validate($value, $validator, $ctx);
            if ($v && !$v->get('valid')) {
                $v = $v->with('attribute', Validation\contextFieldKey($ctx, sprintf('[%d]', $key), ''));
            }
            return $v;
        }, $values);
        $violations = iter\filter(function($v) { return $v; }, $violations);
        $violations = iter\toArray($violations);

        if ($violations) {
            return Validation\violations($violations);
        }
    };
}

/** # Type Validators **/

function typeBoolean() {
    return wrap('boolean', function($v) {
        return is_bool($v) ||
            (is_int($v) && ($v === 0 || $v === 1)) ||
            (is_string($v) && ($v === "1" || $v === "0"));
    });
}

function typeString() {
    return wrap('string', 'is_string');
}

function typeArray() {
    return wrap('array', 'is_array');
}

function typeNumeric() {
    return wrap('numeric', 'is_numeric');
}

function typeInteger() {
    return wrap('integer', 'is_int');
}

function typeDouble() {
    return wrap('double', 'is_double');
}

function typeFloat() {
    return wrap('float', 'is_float');
}

function typeNull() {
    return wrap('null', 'is_null');
}

function digits() {
    return wrap('digits', 'ctype_digits');
}

/** # Regex Validators **/

function regexMatch($pattern, $exclude = false) {
    return function($value) use ($pattern, $exclude) {
        $res = preg_match($pattern, $value);

        /* if it was a match and we want to exclude */
        if ($res && $exclude) {
            return Validation\violate(
                'regex_exclude',
                ['pattern' => $pattern]
            );
        }
        /* if not a match, and we want to match */
        else if (!$res && !$exclude) {
            return Validation\violate(
                'regex_match',
                ['pattern' => $pattern]
            );
        }
    };
}

/** performs simple email format validation */
function regexEmail() {
    $validate = regexMatch('/^.+\@\S+\.\S+$/');
    return function($value) use ($validate) {
        $v = $validate($value);
        if (!$v) {
            return;
        }
        return $v->withCode('regex_email');
    };
}

function regexExclude($pattern) {
    return regexMatch($pattern, true);
}
