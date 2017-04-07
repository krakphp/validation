<?php

namespace Krak\Validation;

use Krak\Cargo;

class Kernel extends Cargo\Container\ContainerDecorator
{
    public function __construct(Cargo\Container $c = null) {
        parent::__construct($c ?: Cargo\container());
        Cargo\register($this, new ValidationServiceProvider());
        $this->with(new ValidationPackage\CoreValidationPackage());
    }

    public function with(ValidationPackage $package) {
        $package->withValidation($this);
    }

    public function validators(array $validators) {
        foreach ($validators as $key => $value) {
            $this['krak.validation.validators'][$key] = $value;
        }
    }

    public function messages(array $messages) {
        $this->pushMessageStore(new MessageStore\ArrayMessageStore($messages));
    }

    public function messageFile($file) {
        $this->pushMessageStore(new MessageStore\LangFileMessageStore($file));
    }

    public function pushMessageStore(MesssageStore $store) {
        $this['krak.validation.messages']->push($store);
    }

    public function defaultMessage($message) {
        $this['krak.validation.default_message'] = $message;
    }

    public function make($validations) {
        return new Validation(
            new ValidationContext\ForceContextValidationContext($this[ValidationContext::class]),
            $this[MessageStore::class],
            $this[FormatMessage::class],
            $this[FormatViolations::class],
            $validations
        );
    }
}
