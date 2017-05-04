<?php

namespace Krak\Validation\ValidationContext;

use Psr\Http\Message\ServerRequestInterface;

class HttpRequestValidationContext extends ValidationContextDecorator
{
    public function validate($value, $validator, array $ctx = []) {
        if ($value instanceof ServerRequestInterface) {
            $value = array_merge(
                $value->getQueryParams(),
                $value->getParsedBody(),
                $value->getUploadedFiles()
            );
        }

        return $this->validation_context->validate($value, $validator, $ctx);
    }
}
