# Krak Validation

The Krak Validation library is a functional and simple approach and interface to validation. It was made out of the need a validation library with a simple interface and configuration. Other leading validators like Respect, Zend, or Symfony have a lot of complexity in their API and adding an actual validator will require adding several classes in some instances.

Krak Validation changes all of this by taking a functional approach to validation where every validator is just a function or instance of `Krak\Validation\Validator`. This design lends itself easily for extending, custom validators, and decorators. It comes bundled with a `Krak\Validation\Kernel` which manages validators to provide a simple, fluent, and customizable interface for utilizing validators.

## Installation

Install with composer at `krak/validation`

## Usage

```php
<?php

$validation = new Krak\Validation\Kernel();
$validator = $validation->make([
    'name' => 'required|string',
    'age' => 'optional|integer|between:18,25',
]);
$violations = $validator->validate([
    'name' => 'RJ',
    'age' => 17,
]);

if ($violations) { // you can also check $validator->failed()
    print_r($violations->format()); // format into an array of error messages
}
```

This will print something like this:

```
Array
(
    [age] => Array
        (
            [0] => The Age field must be between 18 and 25
        )

)
```

## Validators

A validator can be a `Krak\Validation\Validator`, or any callable that accepts a value and returns null on success and a violation on failure.

Validators implement the following signature:

```
function($value, array $ctx = [])
```

The second parameter can typically be left out, but is used to pass additional information into the validator. A good example is a PSR Container so that dependencies can be lazy loaded at time of actual validation.
