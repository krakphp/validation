<?php

namespace Krak\Validation\Exception;

class ViolationException extends ValidationException
{
    public $violation;

    public function __construct($violation) {
        $this->violation = $violation;
        parent::__construct('A violation has occurred.');
    }
}
