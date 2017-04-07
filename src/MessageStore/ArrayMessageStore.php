<?php

namespace Krak\Validation\MessageStore;

use Krak\Validation;

class ArrayMessageStore implements Validation\MessageStore {
    private $messages;

    public function __construct(array $messages = []) {
        $this->messages = $messages;
    }

    public function add($key, $message) {
        $this->messages[$key] = $message;
    }

    public function get(Validation\Violation $v) {
        if ($attribute = $v->get('attribute')) {
            $key = $attribute . '.' . $v->code;
            if (isset($this->messages[$key])) {
                return $this->messages[$key];
            }
            $key = $attribute;
            if (isset($this->messages[$key])) {
                return $this->messages[$key];
            }
        }

        if (isset($this->messages[$v->code])) {
            return $this->messages[$v->code];
        }
    }
}
