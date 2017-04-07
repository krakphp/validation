<?php

namespace Krak\Validation;

use function iter\reduce;

function validate($value, $validator, array $ctx = []) {
    if (isset($ctx['validation']) && $ctx['validation'] instanceof ValidationContext) {
        return $ctx['validation']->validate($value, $validator, $ctx);
    }

    return $validator($value, $ctx);
}

function violate($code, $params = null) {
    return new Violation($code, $params);
}

function violations($violations, $flattened = false) {
    return new ViolationCollection($violations, $flattened);
}

function collection($validators, $err_on_extra = false) {
    return function($value, array $ctx = []) use ($validators, $err_on_extra) {
        if (!is_array($value)) {
            return violate('array');
        }

        if ($err_on_extra && count($value) > count($validators)) {
            return violate('extra_fields');
        }

        $errors = reduce(function($acc, $validator, $key) use ($value, $ctx) {
            $field_key = isset($ctx['field_key']) ? $ctx['field_key'] . '.' . $key : $key;
            if (array_key_exists($key, $value)) {
                $res = validate($value[$key], $validator, array_merge($ctx, [
                    'field_exists' => true,
                    'field_key' => $field_key,
                ]));
            } else {
                $res = validate(null, $validator, array_merge($ctx, [
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
            return violations($errors);
        }
    };
}

function pipe(...$validators) {
    return function($value, array $context = []) use ($validators) {
        foreach ($validators as $validator) {
            $res = validate($value, $validator, $context);

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
            $res = validate($value, $validator, $context);

            if ($res && !$res->get('valid', false)) {
                $violations[] = $res;
            }
        }

        if ($violations) {
            return violations($errors);
        }
    };
}

function optional() {
    return function($value, array $ctx = []) {
        if (!isset($ctx['field_exists'])) {
            return;
        }

        if (!$ctx['field_exists']) {
            return violate('optional')->with('valid', true);
        }
    };
}

function nullable() {
    return function($value, array $ctx = []) {
        if (is_null($value)) {
            return violate('nullable')->with('valid', true);
        }
    };
}

function wrap($func, $name) {
    return function($value) use ($func, $name) {
        if ($func($value)) {
            return;
        }

        return violate($name);
    };
}

function createWrapped($func, $name) {
    return function() use ($func, $name) {
        return wrap($func, $name);
    };
}

function exists() {
    return function($value, array $ctx = []) {
        if (!isset($ctx['field_exists'])) {
            return;
        }

        if (!$ctx['field_exists']) {
            return violate('exists');
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

        return $min <= $value && $value <= $max ? null : violate('between', [
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
                return violate('different', [
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
                return violate('same', [
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
                return violate('fields', [
                    'fields' => $fields,
                ]);
            }
        }
    };
}
