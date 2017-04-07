<?php

namespace Krak\Validation\MessageStore;

use Krak\Validation;

class CompositeMessageStore extends AbstractReadOnlyMessageStore
{
    private $stores;

    public function __construct(array $stores) {
        $this->stores = $stores;
    }

    public function get(Validation\Violation $v) {
        foreach ($this->stores as $store) {
            if ($message = $store->get($v)) {
                return $message;
            }
        }
    }
}
