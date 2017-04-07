<?php

namespace Krak\Validation;

use ArrayObject;
use Krak\Cargo;
use Krak\Invoke;

class ValidationServiceProvider implements Cargo\ServiceProvider
{
    public function register(Cargo\Container $c) {
        $c[ValidationContext\FluentValidationBuilder::class] = function($c) {
            return new ValidationContext\FluentValidationBuilder($c['krak.validation.validators']);
        };
        $c[ValidationContext::class] = function($c) {
            $invoke = new ValidatorInvoke(new Invoke\CallableInvoke());
            $ctx = new ValidationContext\FluentValidationContext(
                new Invoke\ContainerInvoke($invoke, $c->toInterop()),
                $c[ValidationContext\FluentValidationBuilder::class]
            );
            $ctx = new ValidationContext\HttpRequestValidationContext($ctx);
            return $ctx;
        };
        $c[MessageStore::class] = function($c) {
            return new MessageStore\CompositeMessageStore([
                $c['krak.validation.messages'],
                new MessageStore\DefaultMessageStore($c['krak.validation.default_message'])
            ]);
        };
        $c[FormatMessage::class] = function() {
            return new FormatMessage\StrTrFormatMessage();
        };
        $c[FormatViolations::class] = function() {
            return new FormatViolations\MappedMessageFormatViolations();
        };
        $c['krak.validation.messages'] = new MessageStore\StackedMessageStore();
        $c['krak.validation.default_message'] = "The {{attribute}} is invalid.";
        $c['krak.validation.validators'] = new ArrayObject();
    }
}
