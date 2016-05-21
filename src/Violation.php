<?php

namespace Krak\Validation;

/**
 * Violation
 * Simple struct for holding the violation. A violation basically holds the
 * violation code and any data associated with error code
 */
class Violation
{
    public $code;
    public $params;

    public function __construct($code, $params) {
        $this->code = $code;
        $this->params = $params;
    }
}

/** finds a violation in a violation context */
function find_violation($violation, $code, $path = '', $sep = '.') {
    return _find_v_rec($violation, $code, $path ? explode($sep, $path) : []);
}

function _find_v_rec($violation, $code, $path_parts) {
    if ($violation instanceof Violation && \count($path_parts) == 0) {
        return $violation->code == $code ? $violation : null;
    }

    if (!is_array($violation)) {
        return;
    }

    $key = array_shift($path_parts);
    if (!$key) {
        return;
    }

    if (array_key_exists($key, $violation)) {
        return _find_v_rec($violation[$key], $code, $path_parts);
    }
}

function violate($code, $params = [null]) {
    return new Violation($code, $params);
}
