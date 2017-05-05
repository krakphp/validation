<?php

namespace Krak\Validation;

function validate($value, $validator, array $ctx = []) {
    if (isset($ctx['validation']) && $ctx['validation'] instanceof ValidationContext) {
        return $ctx['validation']->validate($value, $validator, $ctx);
    }
    if (is_callable($validator)) {
        return $validator($value, $ctx);
    }
    if ($validator instanceof Validator) {
        return $validator->validate($value, $ctx);
    }

    throw new \InvalidArgumentException("Could not validate with validator because it was not a callable or Validator instance.");
}

function contextGet(array $ctx, $key, $default = null) {
    return array_key_exists($key, $ctx) ? $ctx[$key] : $default;
}

function contextContainer(array $ctx, $key = null) {
    $container = contextGet($ctx, 'container');
    if (!$container || !$container instanceof \Psr\Container\ContainerInterface) {
        throw new \LogicException('No Psr\Container\ContainerInterface instance was found in the validation context.');
    }

    if ($key) {
        return $container->get($key);
    }

    return $container;
}

function contextFieldKey(array $ctx, $key = null, $sep = '.') {
    $field_key = contextGet($ctx, 'field_key');
    if (!$key) {
        return $field_key;
    }
    if (!$field_key) {
        return $key;
    }

    return $field_key . $sep . $key;
}

function violate($code, $params = null) {
    return new Violation($code, $params);
}

function violations($violations) {
    return new ViolationCollection($violations);
}

function arrayArgs(array $args) {
    if (count($args) == 1 && is_array($args[0])) {
        return $args[0];
    }

    return $args;
}
