<?php

namespace Krak\Validation;

function re_match($pattern, $exclude = false)
{
    return function($value) use ($pattern, $exclude) {
        $res = preg_match($pattern, $value);

        /* if it was a match and we want to exclude */
        if ($res && $exclude) {
            return new Violation(
                ViolationCodes::RE_EXCLUDE,
                [$pattern, $value]
            );
        }
        /* if not a match, and we want to match */
        else if (!$res && !$exclude) {
            return new Violation(
                ViolationCodes::RE_MATCH,
                [$pattern, $value]
            );
        }
    };
}

function re_exclude($pattern)
{
    return re_match($pattern, true);
}
