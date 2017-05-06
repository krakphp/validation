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

describe('#between', function() {
    it('validates size is between two values', function() {
        $v = Assert\between(1, 5);
        assertPass($v, [
            4,
            '0',
            'abcde',
            [0, 1, 2, 3, 4]
        ]);
        assertViolation($v, [
            [6, 'between'],
            ['', 'between'],
            [range(1, 6), 'between'],
        ]);
    });
});
describe('#length', function() {
    it('validates the size is exactly a value', function() {
        $v = Assert\length(4);
        assertPass($v, [
            '0123',
            range(1, 4),
            4
        ]);
        assertViolation($v, [
            [5, 'length'],
            ['a', 'length']
        ]);
    });
});
describe('#min', function() {
    it('validates the size is greater than or equal to a minimum', function() {
        $v = Assert\min(1);
        assertPass($v, [
            '0',
            1,
            [0],
        ]);
        assertViolation($v, [
            [0, 'min'],
            ['', 'min'],
            [[], 'min']
        ]);
    });
});
describe('#max', function() {
    it('validates the size is less than or equal to a maximum', function() {
        $v = Assert\max(1);
        assertPass($v, [
            '0',
            1,
            [0],
        ]);
        assertViolation($v, [
            [2, 'max'],
            ['ab', 'max'],
            [[1,2], 'max']
        ]);
    });
});
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
