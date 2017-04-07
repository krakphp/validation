<?php

namespace Krak\Validation\FormatViolations;

use Krak\Validation;
use iter;

class MappedMessageFormatViolations implements Validation\FormatViolations
{
    public function formatViolations(Validation\ViolationCollection $violations) {
        return iter\reduce(function($acc, $v) {
            $attribute = $v->get('attribute', '@');
            $acc[$attribute][] = $v->get('message');
            return $acc;
        }, $violations, []);
    }
}
