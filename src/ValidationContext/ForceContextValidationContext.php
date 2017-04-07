<?php

namespace Krak\Validation\ValidationContext;

use Krak\Validation;

class ForceContextValidationContext extends ValidationContextDecorator
{
    public function validate($value, $validator, array $ctx = []) {
        if (!isset($ctx['validation'])) {
            $ctx['validation'] = $this;
        }

        return $this->validation_context->validate($value, $validator, $ctx);
    }
}
