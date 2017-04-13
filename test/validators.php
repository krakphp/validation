<?php

use Krak\Validation\Validators as Assert;
use Krak\Validation;

function assertPass($validator, array $values, array $ctx = []) {
    foreach ($values as $value) {
        assert(Validation\validate($value, $validator, $ctx) === null);
    }
}

function assertViolation($validator, array $values, array $ctx = []) {
    foreach ($values as list($value, $code)) {
        $v = Validation\validate($value, $validator, $ctx);
        assert($v && $v->code == $code);
    }
}

describe('#digits', function() {
    it('validates only digits', function() {
        $v = Assert\digits();
        assertPass($v, [
            '1',
            '1234'
        ]);
        assertViolation($v, [
            ['12a', 'digits']
        ]);
    });
});
describe('#alpha', function() {
    it('validates only alphabetic chars', function() {
        $v = Assert\alpha();
        assertPass($v, [
            'abc',
            'd'
        ]);
        assertViolation($v, [
            ['1a', 'alpha']
        ]);
    });
});
describe('#alpha_num', function() {
    it('validates only alpha numeric chars', function() {
        $v = Assert\alphaNum();
        assertPass($v, [
            'abc',
            '123',
            'a1b',
        ]);
        assertViolation($v, [
            ['1!a', 'alpha_num']
        ]);
    });
});
describe('#regexEmail', function() {
    it('validates email via simple regular expression', function() {
        $v = Assert\regexEmail();
        assertPass($v, [
            'rj@bighead.net',
            '1.3.23hello@bighead.net',
        ]);
        assertViolation($v, [
            ['bad', 'regex_email']
        ]);
    });
});
