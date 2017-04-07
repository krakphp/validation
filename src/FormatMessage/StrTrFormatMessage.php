<?php

namespace Krak\Validation\FormatMessage;

use Krak\Validation;
use iter;

class StrTrFormatMessage implements Validation\FormatMessage
{
    public function formatMessage(Validation\Violation $v, $message) {
        $params = $v->params;
        if (!$v->has('attribute')) {
            $params['attribute'] = 'value';
        } else {
            $params['attribute'] = ucwords(str_replace(['.', '_'], ' ', $params['attribute'])) . ' field';
        }
        $params = iter\reduce(function($acc, $v, $k) {
            $acc['{{'.$k.'}}'] = $v;
            return $acc;
        }, $params, []);
        return $v->with('message', strtr($message, $params));
    }
}
