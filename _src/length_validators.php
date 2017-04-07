<?php

namespace Krak\Validation;

function length($cmp) {
    return function($value) use ($cmp) {
        $v = $cmp(strlen($value));
        if (!$v) {
            return;
        }

        return $v->withParams($v->params->addData([
            'cmp_msg' => 'length',
        ]));
    };
}

function count($cmp) {
    return function ($value) use ($cmp) {
        $v = $cmp(strlen($value));
        if (!$v) {
            return;
        }

        return $v->withParams($v->params->addData([
            'cmp_msg' => 'count',
        ]));
    };
}

function between($min, $max) {
    return function($a) use ($min, $max) {
        if ($min <= $a && $a <= $max) {
            return;
        }

        return violate(
            ViolationCodes::NOT_BETWEEN,
            new Params([
                'min' => $min,
                'max' => $max
            ])
        );
    };
}

function gt($a) {
    return function($b) use ($a) {
        if ($b > $a) {
            return;
        }

        return violate(
            ViolationCodes::NOT_GREATER_THAN,
            Params::accepted($a)
        );
    };
}

function lt($a) {
    return function($b) use ($a) {
        if ($b < $a) {
            return;
        }

        return violate(
            ViolationCodes::NOT_LESS_THAN,
            Params::accepted($a)
        );
    };
};

function gte($a)
{
    return function($b) use ($a) {
        if ($b >= $a) {
            return;
        }

        return violate(
            ViolationCodes::NOT_GREATER_THAN_OR_EQUAL,
            Params::accepted($a)
        );
    };
}

function lte($a)
{
    return function($b) use ($a) {
        if ($b <= $a) {
            return;
        }

        return violate(
            ViolationCodes::NOT_LESS_THAN_OR_EQUAL,
            Params::accepted($a)
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

            return violate(
                ViolationCodes::NOT_EQUAL,
                Params::accepted($a)
            );
        };
    }
    else {
        return function($b) use ($a) {
            if ($a == $b) {
                return;
            }

            return violate(
                ViolationCodes::NOT_EQUAL,
                Params::accepted($a)
            );
        };
    }
}
