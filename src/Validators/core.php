<?php

namespace Krak\Validation\Validators;

use Krak\Validation;

use iter;

function collection($validators, $err_on_extra = true) {
    return function($value, array $ctx = []) use ($validators, $err_on_extra) {
        if (!is_array($value)) {
            return Validation\violate('array');
        }

        if ($err_on_extra) {
            $value_keys = array_keys($value);
            $validator_keys = array_keys($validators);
            if ($key_diff = array_diff($value_keys, $validator_keys)) {
                return Validation\violate('invalid_keys', [
                    'keys' => implode(', ', $key_diff)
                ]);
            }
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
    $validators = Validation\arrayArgs($validators);
    return function($value, array $context = []) use ($validators) {
        foreach ($validators as $validator) {
            $res = Validation\validate($value, $validator, $context);

            if ($res) {
                return $res;
            }
        }
    };
}

function any(...$validators) {
    $validators = Validation\arrayArgs($validators);
    return function($value, array $context = []) use ($validators) {
        $violations = array_map(function($validator) use ($value, $context) {
            return Validation\validate($value, $validator, $context);
        }, $validators);
        return iter\any(function($v) { return $v == null; }, $violations)
            ? null
            : Validation\violations($violations);
    };
}

function pipeAll(...$validators) {
    $validators = Validation\arrayArgs($validators);
    return function($value, array $context = []) use ($validators) {
        $violations = [];
        foreach ($validators as $validator) {
            $res = Validation\validate($value, $validator, $context);

            if ($res && !$res->get('valid', false)) {
                $violations[] = $res;
            }
        }

        if ($violations) {
            return Validation\violations($violations);
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

function different(...$fields) {
    $fields = Validation\arrayArgs($fields);
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
    $fields = Validation\arrayArgs($fields);
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

/** # Length Validators **/

/** helper to calculate the "size" of a variable. */
function toSize($value) {
    if (is_string($value)) {
        $value = strlen($value);
    } else if (is_array($value)) {
        $value = count($value);
    }

    return $value;
}

function between($min, $max) {
    $min = (int) $min;
    $max = (int) $max;
    return function($value) use ($min, $max) {
        $value = toSize($value);

        return $min <= $value && $value <= $max ? null : Validation\violate('between', [
            'min' => $min,
            'max' => $max
        ]);
    };
}

function length($size) {
    $size = (int) $size;
    return function($value) use ($size) {
        return toSize($value) === $size ? null : Validation\violate('length', [
            'max' => $size,
            'size' => $size,
        ]);
    };
}

function min($min) {
    $min = (int) $min;
    return function($value) use ($min) {
        return toSize($value) >= $min ? null : Validation\violate('min', [
            'min' => $min,
        ]);
    };
}

function max($max) {
    $max = (int) $max;
    return function($value) use ($max) {
        return toSize($value) <= $max ? null : Validation\violate('max', [
            'max' => $max,
        ]);
    };
}

/** # Array Validators **/

/**
 * Validates that a value is inside of an array of accepted values
 */
function inArray(...$accepted) {
    $accepted = Validation\arrayArgs($accepted);
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
        $violations = iter\reduce(function($acc, $value, $key) use ($validator, $ctx) {
            $field_key = Validation\contextFieldKey($ctx, sprintf('[%d]', $key), '');
            $ctx['field_key'] = $field_key;
            $v = Validation\validate($value, $validator, $ctx);
            if ($v && !$v->get('valid')) {
                $v = $v->with('attribute', $field_key);
                $acc[] = $v;
            }
            return $acc;
        }, $values, []);
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
    return wrap('digits', 'ctype_digit');
}

function alpha() {
    return wrap('alpha', 'ctype_alpha');
}

function alphaNum() {
    return wrap('alpha_num', 'ctype_alnum');
}

function number() {
    return wrap('number', function($value) {
        return is_int($value) || is_double($value) || is_float($value);
    });
}

function date() {
    return wrap('date', function($value) {
        return is_string($value) && (bool) strtotime($value);
    });
}

/** # Regex Validators **/

function wrapRegex($code, $pattern) {
    $validate = regexMatch($pattern);
    return function($value) use ($code, $validate) {
        $v = $validate($value);
        if (!$v) {
            return;
        }
        return $v->withCode($code);
    };
}

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
    return wrapRegex('regex_email', '/^.+\@\S+\.\S+$/');
}

function regexExclude($pattern) {
    return regexMatch($pattern, true);
}
