<?php

namespace Krak\Validation;

interface MessageStore {
    /** adds a message into the store */
    public function add($key, $message);
    /** retrieves a message from a violation */
    public function get(Violation $v);
}
