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

describe('Validators', function() {
    describe('#collection', function() {
        it('validates a collection of items', function() {
            $v = Assert\collection([
                'i' => Assert\typeInteger(),
                's' => Assert\typeString(),
            ]);

            assertPass($v, [
                ['i' => 0, 's' => ''],
            ]);
            $violations = $v([]);
            assert($violations[0]->code == 'integer' && $violations[1]->code == 'string');
        });
        it('allows optional fields', function() {
            $v = Assert\collection([
                'i' => Assert\pipe([Assert\optional(), Assert\typeInteger()])
            ]);
            assert($v([]) === null && $v(['i' => null])[0]->code == 'integer');
        });
        it('errs on extra keys', function() {
            $v = Assert\collection([
                'a' => Assert\pipe([Assert\optional(), Assert\typeInteger()]),
            ]);
            $err = $v(['b' => 1, 'c' => 2]);
            assert($err->code == 'invalid_keys' && $err->params['keys'] == 'b, c');
        });
    });
    describe('#pipe', function() {
        it('pipes validators one after the next', function() {
            $v = Assert\pipe([Assert\typeInteger(), Assert\max(5)]);
            assertPass($v, [1]);
            assertViolation($v, [
                ['3', 'integer'],
                [6, 'max'],
            ]);
        });
    });
    describe('#pipeAll', function() {
        it('pipes all validators one after the next regardless if one fails', function() {
            $vs = Assert\pipeAll([Assert\typeString(), Assert\typeArray()])(1);
            assert(count($vs) == 2);
            assert($vs[0]->code == "string");
            assert($vs[1]->code == "array");
        });
    });
    describe('#any', function() {
        $v = Assert\any([Assert\typeInteger(), Assert\typeString()]);
        it('passes if any of the validators pass', function() use ($v) {
            assertPass($v, [1, 'a']);
        });
        it('fails if all of the validators fail', function() use ($v) {
            $vlts1 = $v(true);
            $vlts2 = $v([]);

            assert(count($vlts1) === count($vlts2) && $vlts1[0]->code == 'integer' && $vlts1[1]->code == 'string');
        });
    });

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
    describe('#alphaNum', function() {
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
    describe('#double', function() {
        it('validates that the type of a var is double', function() {
            $v = Assert\typeDouble();
            assertPass($v, [1.0, (double) 1, (float) 1]);
            assertViolation($v, [
                [1, 'double'],
                ['a', 'double'],
                [(int) 1.1, 'double'],
            ]);
        });
    });
    describe('#number', function() {
        it('validates that a var is a number', function() {
            $v = Assert\number();
            assertPass($v, [1, 1.0, (double) 1, (float) 1]);
            assertViolation($v, [
                ['a', 'number'],
                [[], 'number'],
                [new stdClass(), 'number'],
            ]);
        });
    });
    describe('#date', function() {
        it('validates a valid date string', function() {
            $v = Assert\date();
            assertPass($v, [
                '2017-07-09 00:12:00',
                '2017',
            ]);
            assertViolation($v, [
                [1, 'date'],
                ['-b123-5-a', 'date'],
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
});
