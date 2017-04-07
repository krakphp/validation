<?php

namespace Krak\Validation;

class Violation
{
    use ViolationParams;

    public $code;

    public function __construct($code, $params = null) {
        $this->code = $code;
        $this->params = $params;
    }

    public function withCode($code) {
        $v = clone $this;
        $v->code = $code;
        return $v;
    }

    public function flatten() {
        return new ViolationCollection([$this]);
    }
}
