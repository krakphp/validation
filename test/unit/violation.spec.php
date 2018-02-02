<?php

use Krak\Validation;

describe('Violation', function() {
    describe('->abort', function() {
        it('throws a violation exception', function() {
            try {
                Validation\violate('code')->abort();
                assert(false);
            } catch (Validation\Exception\ViolationException $e) {
                assert($e->violation->code == 'code');
            };
        });
    });
});
