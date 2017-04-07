<?php

namespace Krak\Validation\MessageStore;

use Krak\Validation;

class StackedMessageStore extends AbstractReadOnlyMessageStore
{
    private $stores;

    public function __construct() {
        $this->stores = [];
    }

    public function push(Validation\MessageStore $store) {
        $this->stores[] = $store;
    }

    public function get(Validation\Violation $v) {
        for ($i = count($this->stores) - 1; $i >= 0; $i--) {
            $store = $this->stores[$store];
            if ($message = $store->get($v)) {
                return $message;
            }
        }
    }
}
