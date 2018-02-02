<?php

use Krak\Validation;

describe('Krak\Validation', function() {
    it('can create and validate values', function() {
        $validation = new Validation\Kernel();
        $violations = $validation->make('integer')->validate("a");
        expect($violations)->length(1);
        expect($violations[0]->code)->equal('integer');
    });
    it('can validate array structures', function() {
        $validation = new Validation\Kernel();
        $violations = $validation->make([
            'id' => 'required|integer',
            'name' => 'optional|string',
        ])->validate([
            'id' => 1
        ]);
        expect($violations)->equal(null);
    });
});
