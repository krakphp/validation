# Krak Validation

The Krak Validation library is a functional and simple approach and interface to validation. It was made out of the need a validation library with a simple interface and configuration. Other leading validators like Respect, Zend, or Symfony have a lot of complexity in their API and adding an actual validator will require adding several classes in some instances.

Krak Validation changes all of this by taking a functional approach to validation where every validator is just a function or instance of `Krak\Validation\Validator`. This design lends itself easily for extending, custom validators, and decorators. It comes bundled with a `Krak\Validation\Kernel` which manages validators to provide a simple, fluent, and customizable interface for utilizing validators.

- [Installation](#installation)
- [Usage](#usage)
- [Validators](#validators)
    - [Violations](#violations)
    - [Throwing Violations](#throwing-violations)
- [Validation Packages](#validation-packages)
    - [Creating a Validation Package](#creating-a-validation-package)
- [Core Validation Package](#core-validation-package)
- [Doctrine Validation Package](#doctrine-validation-package)
- [API](#api)


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

### Violations

Creating violations is easy with the `violate` or `violations` function. For most validators, you'll simply be creating one violation.

```php
<?php

use Krak\Validation;

/** enforces that a value equals a specific value */
function equalsStringValidator($match) {
    return function($value, array $ctx = []) use ($match) {
        if ($value == $match) {
            return;
        }

        return Validation\violate('equals_string', [
            'match' => $match
        ]);
    };
}

$v = equalsStringValidator('foo');
$v('foo'); // returns null
$violation = $v('bar'); // return a Krak\Validation\Violation
```

In some cases, one validator can return multiple violations. In that case, we just use the `violations` to create a violation collection. You can checkout the `Krak\Validation\Validators\collection` validator to see how to create a violation collection.

### Throwing Violations

Once you have a violation or a violation collection, you can optionally throw them as exceptions to be handled upstream.

```php
<?php

try {
    $violation->abort();
} catch (Krak\Validation\Exception\ViolationException $e) {
    assert($e->violation === $violation);
}
```

### Asserting Data

A very common practice is using the Validation Kernel to make and validate domain data and then throw a ViolationException if any violations occur. This can be done simply via the `assert` method.

```
$validation = new Krak\Validation\Kernel();
$validation->make([
    'name' => 'required|string',
    'age' => 'optional|integer|between:18,25',
])->assert(['name' => 'RJ', 'age' => 100]);
// this will have thrown a ViolationException due to the age constraint
```

You can then easily catch the `ViolationException` upstream and format the violation into readable errors:

```
try {
    return response($storeUser($userData), 201);
} catch (Krak\Validation\Exception\ViolationException $e) {
    return response([
        'errors' => $e->violation->format(),
    ], 422);
}
```

## Validation Packages

Validation Packages are simple extensions to the Validation\Kernel that register validators and messages into the system.

### Creating a Validation Package

To create a validation package, you need to extend the `ValidationPackage` interface.

```php
interface ValidationPackage
{
    public function withValidation(Kernel $validation);
}
```

From there, you can configure the validation kernel any which way you need.

Typically, you'll just use the `validators`, `messages`, and `aliases` methods to add keyed validations and corresponding methods or aliases.

An example Validation Package would look like:

```php
<?php

use Krak\Validation;

class AcmeValidationPackage implements Validation\ValidationPackage
{
    public function withValidation(Validation\Kernel $v) {
        $v->validators([
            'integer' => 'intValidator', // name of intValidator func
            'min' => MinValidator::class // name of MinValidator class
        ]);
        $v->messages([
            'integer' => 'The {{attribute}} is not a valid integer',
            'min' => 'The {{attribute}} size must be greater than or equal to {{min}}',
        ]);
        $v->aliases([
            'int' => 'integer',
        ]);
    }
}
```

The validators would be defined in different files like so:

```php
use Krak\Validation;

// intValidator.php
function intValidator() {
    return function($v) {
        return !is_int($v) ? Validation\violate('integer') : null;
    };
}

// MinValidator.php
class MinValidator implements Validation\Validator
{
    private $min;

    public function __construct($min) {
        $this->min = $min;
    }

    public function validate($value, array $context = []) {
        return $value < $min ? Validation\violate('min', ['min' => $this->min]) : null;
    }
}
```

## Core Validation Package

The Core Validation package defines a bunch of the normal useful validators.

<table>
    <tr>
        <td><a href="#validator-all">all</a></td>
        <td><a href="#validator-alpha">alpha</a></td>
        <td><a href="#validator-alpha_num">alpha_num</a></td>
        <td><a href="#validator-array">array</a></td>
    </tr>
    <tr>
        <td><a href="#validator-between">between</a></td>
        <td><a href="#validator-boolean">boolean</a></td>
        <td><a href="#validator-date">date</a></td>
        <td><a href="#validator-digits">digits</a></td>
    </tr>
    <tr>
        <td><a href="#validator-double">double</a></td>
        <td><a href="#validator-email">email</a></td>
        <td><a href="#validator-exists">exists</a></td>
        <td><a href="#validator-float">float</a></td>
    </tr>
    <tr>
        <td><a href="#validator-in">in</a></td>
        <td><a href="#validator-integer">integer</a></td>
        <td><a href="#validator-length">length</a></td>
        <td><a href="#validator-max">max</a></td>
    </tr>
    <tr>
        <td><a href="#validator-min">min</a></td>
        <td><a href="#validator-null">null</a></td>
        <td><a href="#validator-nullable">nullable</a></td>
        <td><a href="#validator-number">number</a></td>
    </tr>
    <tr>
        <td><a href="#validator-numeric">numeric</a></td>
        <td><a href="#validator-optional">optional</a></td>
        <td><a href="#validator-regex">regex</a></td>
        <td><a href="#validator-regex_exclude">regex_exclude</a></td>
    </tr>
    <tr>
        <td><a href="#validator-required">required</a></td>
        <td><a href="#validator-string">string</a></td>
    </tr>
</table>

<div id="validator-all"></div>

### all

Validates an array with the given validator. If any element in the array fails the validation, a violation will be returned.

**Definition:** `Krak\Validation\Validators\forAll($validator)`

**Simple Usage:**

```php
$validator->make('all:integer')->validate([1,2,3]);
```

**Advanced Usage:**

```php
use function Krak\Validation\Validators\{forAll};
$validator->make(forAll('integer|between:1,3'))->validate([1,2,3])
```

**Standalone Usage:**

```php
use function Krak\Validation\Validators\{forAll, typeInteger};

forAll(typeInteger())([2,3]);
```

<div id="validator-alpha"></div>

### alpha

Wraps the `ctype_alpha` function and validates a string to verify only alpha characteres.

**Definition:** `Krak\Validation\Validators\alpha()`

**Simple Usage:**

```php
$validator->make('alpha')->validate('abc');
```

**Standalone Usage:**

```php
use function Krak\Validation\Validators\{alpha};

alpha()('123');
```


<div id="validator-alpha_num"></div>

### alpha_num

Wraps the `ctype_alnum` function and validates a string to make sure it's alpha numeric.

**Definition:** `Krak\Validation\Validators\alphaNum()`

**Simple Usage:**

```php
$validator->make('alpah_num')->validate('abc123');
```

**Standalone Usage:**

```php
use function Krak\Validation\Validators\{alphaNum};
alphaNum()('abc123');
```

<div id="validator-array"></div>

### array

Verifies that the value is an array using `is_array`.

**Definition:** `Krak\Validation\Validators\typeArray()`

**Simple Usage:**
```php
$validator->make('array')->validate();
```

**Standalone Usage:**

```php
use function Krak\Validation\Validators\{typeArray};

typeArray()([]);
```

<div id="validator-between"></div>

### between

Validates a value's [size](#tosizevalue) is between two values inclusively.

**Definition:** `Krak\Validation\Validators\between($min, $max)`

**Simple Usage:**
```php
$validator->make('between:1,2')->validate(2);
```

**Standalone Usage:**

```php
use function Krak\Validation\Validators\{between};

between(1, 2)(2);
```

<div id="validator-boolean"></div>

### boolean

Validates a value is a boolean. `true`, `false`, `"0"`, `"1"`, `0`, `1` are all instances of boolean.

**Definition:** `Krak\Validation\Validators\typeBoolean()`

**Simple Usage:**
```php
$validator->make('boolean')->validate(true);
```

**Standalone Usage:**

```php
use function Krak\Validation\Validators\{typeBoolean};

typeBoolean()(true);
```

<div id="validator-date"></div>

### date

Validates a string is a valid date using `strototime`. Anything that `strtotime` accepts is accepted here.

**Definition:** `Krak\Validation\Validators\date()`

**Simple Usage:**
```php
$validator->make('date')->validate('2017-08-11');
```

**Standalone Usage:**

```php
use function Krak\Validation\Validators\{date};

date()('2017-08-11');
```

<div id="validator-digits"></div>

### digits

Validates a string is of type digits using the `ctype_digits` function.

**Definition:** `Krak\Validation\Validators\digits()`

**Simple Usage:**
```php
$validator->make('digits')->validate('123');
```

**Standalone Usage:**

```php
use function Krak\Validation\Validators\{digits};

digits()('123');
```

<div id="validator-double"></div>

### double

Validates a value is a double using `is_double`.

**Definition:** `Krak\Validation\Validators\double()`

**Simple Usage:**
```php
$validator->make('double')->validate(4.2);
```

**Standalone Usage:**

```php
use function Krak\Validation\Validators\{double};

double()(4.2);
```

<div id="validator-email"></div>

### email

Validates that a string matches an email regex.

**Definition:** `Krak\Validation\Validators\regexEmail()`

**Simple Usage:**
```php
$validator->make('email')->validate('username@gmail.com');
```

**Standalone Usage:**

```php
use function Krak\Validation\Validators\{regexEmail};

regexEmail()('username@gmail.com');
```

<div id="validator-exists"></div>

### exists

Alias of [`required`](#validator-requried).

<div id="validator-float"></div>

### float

Validates a value is a float using `is_float`.

**Definition:** `Krak\Validation\Validators\float()`

**Simple Usage:**
```php
$validator->make('float')->validate(4.2);
```

**Standalone Usage:**

```php
use function Krak\Validation\Validators\{float};

float()(4.2);
```

<div id="validator-in"></div>

### in

Validates if a values is within a given array.

**Definition:** `Krak\Validation\Validators\inArray`

**Simple Usage:**
```php
$validator->make('in:a,b,c')->validate('b');
```

**Standalone Usage:**

```php
use function Krak\Validation\Validators\{inArray};

inArray(['a', 'b', 'c'])('b');
```

<div id="validator-integer"></div>

### integer

Validates that a value is an integer.

**Definition:** `Krak\Validation\Validators\typeInteger()`

**Simple Usage:**
```php
$validator->make('integer')->validate(1);
```

**Standalone Usage:**

```php
use function Krak\Validation\Validators\{typeInteger};

typeInteger()(1);
```

<div id="validator-length"></div>

### length

Validates that a value's [size](#sizetovalue) is exactly the given length.

**Definition:** `Krak\Validation\Validators\length($size)`

**Simple Usage:**
```php
$validator->make('length:3')->validate('abc');
```

**Standalone Usage:**

```php
use function Krak\Validation\Validators\{length};

length()('abc');
```

<div id="validator-max"></div>

### max

Validates that a value's [size](#tosizevalue) is less than or equal to a given max.

**Definition:** `Krak\Validation\Validators\max($max)`

**Simple Usage:**
```php
$validator->make('max:5')->validate(4);
```

**Standalone Usage:**

```php
use function Krak\Validation\Validators\{max};

max(5)(4);
```

<div id="validator-min"></div>

### min

Validates that a value's [size](#tosizevalue) is greater than or equal to a given min.

**Definition:** `Krak\Validation\Validators\min($min)`

**Simple Usage:**
```php
$validator->make('min:2')->validate(3);
```

**Standalone Usage:**

```php
use function Krak\Validation\Validators\{min};

min(2)(3);
```

<div id="validator-null"></div>

### null

Validates that a value's type is null.

**Definition:** `Krak\Validation\Validators\typeNull()`

**Simple Usage:**
```php
$validator->make('null')->validate(null);
```

**Standalone Usage:**

```php
use function Krak\Validation\Validators\{typeNull};

typeNull()(null);
```

<div id="validator-nullable"></div>

### nullable

Validates that a type is null or not. This is typically used in a chain of validators and will stop validation if the value is null or let the validation continue on else.

**Definition:** `Krak\Validation\Validators\nullable()`

**Simple Usage:**

```php
$v = $validator->make('nullable|integer');
$v->validate(null);
$v->validate(1);
```

**Standalone Usage:**

```php
use function Krak\Validation\Validators\{pipe, nullable, typeInteger};

$v = pipe([nullable(), typeInteger()]);
$v(null);
$v(1);
```
<div id="validator-number"></div>

### number

Validates that a given value is either a float, double, or integer. This is not the same as `is_numeric`.

**Definition:** `Krak\Validation\Validators\number()`

**Simple Usage:**
```php
$validator->make('number')->validate(1);
```

**Standalone Usage:**

```php
use function Krak\Validation\Validators\{number};

number()(1);
```

<div id="validator-numeric"></div>

### numeric

Validates that a given value is numeric using the `is_numeric` function.

**Definition:** `Krak\Validation\Validators\typeNumeric()`

**Simple Usage:**
```php
$validator->make('numeric')->validate('1.5');
```

**Standalone Usage:**

```php
use function Krak\Validation\Validators\{typeNumeric};

typeNumeric()('1.5');
```

<div id="validator-optional"></div>

### optional

Validates that a keyed array is optional and is not required to be present in the array. This validator only make sense within the context of a collection.

**Definition:** `Krak\Validation\Validators\optional()`

**Simple Usage:**
```php
$v = $validator->make([
    'id' => 'optional|integer'
]);
$v->validate([]);
$v->validate(['id' => 1])
```

**Standalone Usage:**

```php
use function Krak\Validation\Validators\{collection, optional, typeInteger, pipe};

$v = collection([
    'id' => pipe([optional(), typeInteger()])
]);
$v([]);
$v(['id' => 1]);
```


<div id="validator-regex"></div>

### regex

Validates that a given value is either a float, double, or integer. This is not the same as `is_numeric`.

**Definition:** `Krak\Validation\Validators\regexMatch($regex, $exclude = false)`

**Simple Usage:**

```php
$validator->make('regex:/a+b/')->validate('aaab');
```

**Note** defining a regex via the string can be tricky because of how the validation rule parser will validate `|`, `:`, and `,`. You'll almost always want to call use actual validator itself.

**Advanced Usage:**

```php
use function Krak\Validation\Validators\{regexMatch};

$validator->make(regexMatch('/(aa|bb)/'))->validate('aa');
```

**Standalone Usage:**

```php
use function Krak\Validation\Validators\{regexMatch};

regexMatch('/(aa|bb)/')('aa');
```

<div id="validator-regex_exclude"></div>

### regex_exclude

Exactly like `regex` except it matches that the value excludes the specific regular expression.

**Definition:** `Krak\Validation\Validators\regexExclude($regex)`

**Simple Usage:**

```php
$validator->make('regex:/a+b/')->validate('c');
```

**Note** defining a regex via the string can be tricky because of how the validation rule parser will validate `|`, `:`, and `,`. You'll almost always want to call use actual validator itself.

**Advanced Usage:**

```php
use function Krak\Validation\Validators\{regexExclude};

$validator->make(regexExclude('/(aa|bb)/'))->validate('cc');
```

**Standalone Usage:**

```php
use function Krak\Validation\Validators\{regexExclude};

regexExclude('/(aa|bb)/')('cc');
```

<div id="validator-required"></div>

### required

Validates that a given value is required in a collection. This validator only makes sense within the context of a collection.

**Definition:** `Krak\Validation\Validators\required()`

**Simple Usage:**

```php
$validator->make([
    'id' => 'required|integer',
])->validate(['id' => 1]);
```

**Standalone Usage:**

```php
use function Krak\Validation\Validators\{collection, pipe, typeInteger, required};

collection([
    'id' => pipe([required(), typeInteger()])
])(['id' => 1]);
```

<div id="validator-string"></div>

### string

Validates that a given value is a string.

**Definition:** `Krak\Validation\Validators\typeString()`

**Simple Usage:**
```php
$validator->make('string')->validate('a');
```

**Standalone Usage:**

```php
use function Krak\Validation\Validators\{typeString};

typeString()('a');
```

## Doctrine Validation Package

The Doctrine Validation Package defines doctrine related validators.

<table>
    <tr>
        <td><a href="#validator-doctrine_all_exist">doctrine_all_exist</a></td>
        <td><a href="#validator-doctrine_entities">doctrine_entities</a></td>
        <td><a href="#validator-doctrine_entity">doctrine_entity</a></td>
        <td><a href="#validator-doctrine_exists">doctrine_exists</a></td>
    </tr>
    <tr>
        <td><a href="#validator-doctrine_unique">doctrine_unique</a></td>
        <td><a href="#validator-doctrine_unique_entity">doctrine_unique_entity</a></td>
    </tr>
</table>

To enable the doctrine package, you can do the following:

```php
$validation = new Krak\Validation\Kernel();
$validation->withDoctrineValidators();
// only needed for doctrine_entities, doctrine_entity, and doctrine_unique_entity
$validation['Doctrine\Common\Persistence\ObjectManager'] = function() {
    // return a configured entity manager here...
};
$validation['Doctrine\DBAL\Connection'] = function() {
    // return a dbal connection here
};
$validation->context([
    'doctrine.model_prefix' => Acme\Model::class,
]);
```

<div id="validator-doctrine_all_exist"></div>

### doctrine_all_exist

Validates that a set of strings or integers all exist in a given table.

**Definition:** `class Krak\Validation\Validators\Doctrine\AllExist($table_name, $field = 'id', $type = 'int')`

**Simple Usage:**

```php
$validator->make('array|all:integer|doctrine_all_exist:users')->validate([1,2,3]);
```

**Standalone Usage:**

```php
use Krak\Validation\Validators\Doctrine\AllExist;
use function Krak\Validation\Validators\{pipe, typeInteger, forAll};

pipe([forAll(typeInteger), new AllExist('users')])([1,2,3]);
```

<div id="validator-doctrine_entities"></div>

### doctrine_entities

Validates that a a set of ORM Entities exist from a unique key. The given entity name is joined with the `doctrine.model_prefix` if one is given.

**Definition:** `class Krak\Validation\Validators\Doctrine\Entities($entity_name, $field = 'id')`

**Simple Usage:**

```php
$validator->make('array|all:integer|doctrine_entities:User')->validate([1,2,3]);
```

**Standalone Usage:**

```php
use Krak\Validation\Validators\Doctrine\Entities;
use function Krak\Validation\Validators\{pipe, typeInteger, forAll};

pipe([forAll(typeInteger), new Entities('User')])([1,2,3]);
```

<div id="validator-doctrine_entity"></div>

### doctrine_entity

Validates that an entity exists in the db with the given value.

**Definition:** `class Krak\Validation\Validators\Doctrine\Entity($entity_name, $field = 'id')`

**Simple Usage:**

```php
$validator->make('doctrine_entity:User')->validate(1);
```

**Standalone Usage:**

```php
use Krak\Validation\Validators\Doctrine\Entity;

(new Entity('User'))->validate(1);
```

<div id="validator-doctrine_exists"></div>

### doctrine_exists

Validates that a given value exists in a table in a certain field.

**Definition:** `class Krak\Validation\Validators\Doctrine\Exists($table_name, $field = 'id')`

**Simple Usage:**

```php
$validator->make('doctrine_entity:users')->validate(5);
```

**Standalone Usage:**

```php
use Krak\Validation\Validators\Doctrine\Exists;

(new Exists('users'))->validate(5);
```

<div id="validator-doctrine_unique"></div>

### doctrine_unique

Validates that a given value is unique and doesn't exist in a table in a field.

**Definition:** `class Krak\Validation\Validators\Doctrine\Unique($table_name, $field = 'id')`

**Simple Usage:**

```php
$validator->make('doctrine_unique:users,email')->validate('username@gmail.com');
```

**Standalone Usage:**

```php
use Krak\Validation\Validators\Doctrine\Unique;

(new Unique('users', 'email'))->validate('username@gmail.com');
```

<div id="validator-doctrine_unique_entity"></div>

### doctrine_unique_entity

Validates that a given value exists in a table in a certain field.

**Definition:** `class Krak\Validation\Validators\Doctrine\UniqueEntity($entity_name, $field = 'id')`

**Simple Usage:**

```php
$validator->make('doctrine_unique_entity:User,email')->validate('username@gmail.com');
```

**Standalone Usage:**

```php
use Krak\Validation\Validators\Doctrine\UniqueEntity;

(new UniqueEntity('User', 'email'))->validate('username@gmail.com');
```

## API

All of the following are defined in the `Krak\Validation\Validators` namespace.

### `collection($validators, $err_on_extra = true)`

Validates a map of validators with attribute names mapping to other validators. `$err_on_extra` is a flag that will determine whether or not to validate if extra fields are in the input array.

This function will return either a Violation, ViolationCollection, or null depending on the input value.

### `toSize($value)`

Gets the size of a variable. If string, it returns the string length. If array, it returns the count, else it assumes numeric and returns the value itself.
