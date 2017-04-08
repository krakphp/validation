<?php

namespace Krak\Validation\MessageStore;

use Krak\Validation;

class DefaultMessageStore extends AbstractReadOnlyMessageStore {
    private $message;

    public function __construct($message) {
        $this->message = $message;
    }

    public function get(Validation\Violation $v) {
        return $this->message;
    }
}
