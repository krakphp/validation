<?php

namespace Krak\Validation\MessageStore;

use Krak\Validation;

class LangFileMessageStore extends AbstractReadOnlyMessageStore
{
    private $file_path;
    private $store;

    public function __construct($file_path) {
        $this->file_path = $file_path;
    }

    public function get(Validation\Violation $v) {
        if (!$this->store) {
            $this->loadLangFile();
        }

        return $this->store->get($v);
    }

    private function loadLangFile() {
        $this->store = new ArrayMessageStore(require_once $this->file_path);
    }
}
