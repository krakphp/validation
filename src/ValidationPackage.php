<?php

namespace Krak\Validation;

interface ValidationPackage {
    public function withValidation(Kernel $v);
}
