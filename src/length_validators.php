<?php

namespace Krak\Validation;

function length($cmp)
{
    return function($value) use ($cmp) {
        return validate($cmp, strlen($value));
    };
}

function count($cmp) {
    return function ($value) use ($cmp) {
        return validate($cmp, \count($value));
    };
}

function gt($a) {
    return function($b) use ($a) {
        if ($b > $a) {
            return;
        }

        return new Violation(
            ViolationCodes::NOT_GREATER_THAN,
            [$a, $b]
        );
    };
}

function lt($a) {
    return function($b) use ($a) {
        if ($b < $a) {
            return;
        }

        return new Violation(
            ViolationCodes::NOT_LESS_THAN,
            [$a, $b]
        );
    };
};

function gte($a)
{
    return function($b) use ($a) {
        if ($b >= $a) {
            return;
        }

        return new Violation(
            ViolationCodes::NOT_GREATER_THAN_OR_EQUAL,
            [$a, $b]
        );
    };
}

function lte($a)
{
    return function($b) use ($a) {
        if ($b <= $a) {
            return;
        }

        return new Violation(
            ViolationCodes::NOT_LESS_THAN_OR_EQUAL,
            [$a, $b]
        );
    };
}

function eq($a, $strict=true)
{
    if ($strict) {
        return function($b) use ($a) {
            if ($a === $b) {
                return;
            }

            return new Violation(
                ViolationCodes::NOT_EQUAL,
                [$a, $b]
            );
        };
    }
    else {
        return function($b) use ($a) {
            if ($a == $b) {
                return;
            }

            return new Violation(
                ViolationCodes::NOT_EQUAL,
                [$a, $b]
            );
        };
    }
}
