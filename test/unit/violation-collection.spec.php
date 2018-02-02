<?php

use Krak\Validation;

describe("ViolationCollection", function() {
    it('implements ArrayAccess', function() {
        $violations = Validation\violations([
            Validation\violate('error_1'),
            Validation\violate('error_2'),
        ]);
        assert(!isset($violations[2]) && $violations[0]->code == 'error_1' && $violations[1]->code == 'error_2');
    });
});
