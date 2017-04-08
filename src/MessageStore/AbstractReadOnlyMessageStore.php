<?php

namespace Krak\Validation\MessageStore;

use Krak\Validation;

abstract class AbstractReadOnlyMessageStore implements Validation\MessageStore
{
    public function add($key, $message) {
        throw new \LogicException("Cannot add a message to a read only message store.");
    }

    abstract public function get(Validation\Violation $v);
}
