<?php

namespace Krak\Validation\Exception;

class ViolationException extends \Exception
{
    public $violation;

    public function __construct($violation) {
        $this->violation = $violation;
        parent::__construct('A violation has occurred.');
    }
}
