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

    public function __construct($code, $params)
    {
        $this->code = $code;
        $this->params = $params;
    }
}
