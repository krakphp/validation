<?php

namespace Krak\Validation;

class Validation implements FormatMessage, FormatViolations {
    private $validation_context;
    private $message_store;
    private $format_message;
    private $format_violations;
    private $validations;

    private $result;
    private $has_run;

    public function __construct(ValidationContext $validation_context, MessageStore $message_store, FormatMessage $format_message, FormatViolations $format_violations, $validations) {
        $this->validation_context = $validation_context;
        $this->message_store = $message_store;
        $this->format_message = $format_message;
        $this->format_violations = $format_violations;
        $this->validations = $validations;
        $this->has_run = false;
    }

    public function formatMessage(Violation $v, $message) {
        return $this->format_message->formatMessage($v, $message);
    }

    public function formatViolations(ViolationCollection $violations) {
        return $this->format_violations->formatViolations($violations);
    }

    public function getMessage(Violation $v) {
        return $this->message_store->get($v);
    }

    public function messages($data) {
        $this->message_store = new MessageStore\CompositeMessageStore([
            new MessageStore\ArrayMessageStore($data),
            $this->message_store
        ]);
        return $this;
    }

    public function validate($value, array $ctx = []) {
        $violation = $this->validation_context->validate($value, $this->validations, $ctx);
        $this->has_run = true;
        if (!$violation) {
            return;
        }

        $this->result = $violation->flatten()
            ->with('validation', $this)
            ->formatMessages();
        return $this->result;
    }

    public function failed() {
        if (!$this->has_run) {
            throw new \LogicException("Cannot check if validation failed when the validation has not run yet.");
        }
        return $this->result !== null;
    }

    public function violations() {
        if (!$this->has_run) {
            throw new \LogicException("Cannot grab violations when the validation has not run yet.");
        }
        return $this->result;
    }
}
