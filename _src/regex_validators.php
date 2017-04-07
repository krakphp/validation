<?php

namespace Krak\Validation;

function re_match($pattern, $exclude = false)
{
    return function($value) use ($pattern, $exclude) {
        $res = preg_match($pattern, $value);

        /* if it was a match and we want to exclude */
        if ($res && $exclude) {
            return violate(
                ViolationCodes::RE_EXCLUDE,
                Params::accepted($pattern)
            );
        }
        /* if not a match, and we want to match */
        else if (!$res && !$exclude) {
            return violate(
                ViolationCodes::RE_MATCH,
                Params::accepted($pattern)
            );
        }
    };
}

/** performs simple email format validation */
function re_email() {
    $validate = re_match('/^.+\@\S+\.\S+$/');
    return function($value) use ($validate) {
        $v = $validate($value);
        if (!$v) {
            return;
        }


    };
}

function re_exclude($pattern)
{
    return re_match($pattern, true);
}
