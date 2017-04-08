<?php

namespace Krak\Validation\ValidationContext;

use Psr\Http\Message\ServerRequestInterface;

class HttpRequestValidationContext extends ValidationContextDecorator
{
    public function validate($value, $validator, array $ctx = []) {
        if ($value instanceof ServerRequestInterface) {
            $value = array_merge(
                $req->getQueryParams(),
                $req->getParsedBody(),
                $req->getUploadedFiles()
            );
        }

        return $this->validation_context->validate($value, $validator, $ctx);
    }
}
