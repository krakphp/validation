<?php

use Krak\Validation\{
    ValidationContext\FluentValidationBuilder,
    Validators as Assert,
    Exception\CircularReferenceException
};

describe('Validation Context', function() {
    describe("FluentValidationBuilder", function() {
        it('can resolve validators from strings', function() {
            $builder = new FluentValidationBuilder([
                'int' => Assert\typeInteger::class,
                'min' => Assert\min::class,
                'max' => Assert\max::class,
            ]);

            $validate = $builder->buildValidator('int|min:1|max:3');
            expect($validate(2))->equal(null);
            expect($validate(0)->code)->equal('min');
        });
        it('can resolve aliases', function() {
            $builder = new FluentValidationBuilder([
                'int' => Assert\typeInteger::class,
                'min' => Assert\min::class,
                'max' => Assert\max::class,
            ], ['i' => 'int']);

            $validate = $builder->buildValidator('i');
            expect($validate(2))->equal(null);
            expect($validate('2')->code)->equal('integer');
        });
        it('can detect circular alias references', function() {
            expect(function() {
                $builder = new FluentValidationBuilder([], ['i' => 'int', 'int' => 'i']);
                $validate = $builder->buildValidator('i');
                $validate(1);
            })->throw(CircularReferenceException::class);
        });
    });
});
