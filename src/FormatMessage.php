<?php

namespace Krak\Validation;

interface FormatMessage {
    public function formatMessage(Violation $v, $message);
}
