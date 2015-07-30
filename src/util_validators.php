<?php

namespace Krak\Validation;

use Closure;

/**
 * Performs a validation on any type of validator on the given value. Useful
 * if you use different types of validators (Closure, callable, or Validator instance)
 */
function validate($validator, $value) {
    if ($validator instanceof Validator) {
        return $validator->validateValue($value);
    }
    else if ($validator instanceof Closure) {
        return $validator($value);
    }
    else if (is_callable($validator)) {
        return call_user_func($validator, $value);
    }
    else {
        throw new InvalidValidatorException();
    }
}

/**
 * Parses a string of flag values and returns booleans of which flags existed
 * or not. The parser just looks for a string to detemine if the flag exists,
 * so you can pass multiple flags in by concatenating them togehter with nothing
 * or a separater if you choose
 *
 *      // all three of these will work the same for parsing a o and p flag
 *      collection_parse_flags('op');
 *      collection_parse_flags('o,p');
 *      collection_parse_flags('o|p');
 *
 * Valid flags are: optional
 */
function collection_parse_flags($flags)
{
    return [
        'optional' => strpos($flags, 'o') !== false
    ];
}

/**
 * validates a collection by passing in a colleciton of validators.
 * The `$key => $validator` of the collection needs to match the `$key => $value` in
 * the collection to be validated. the $validator may be any validator or a tuple
 * [$flags, $validator] where $flags is just a string of flags to be parsed from
 * `collection_parse_flags` and $validator is the validator.
 * @param $coll a collection of keys to validators
 * @param bool $err_on_missing whether or not to return a missing_field violation if the field
 *  is missing
 */
function collection($coll, $err_on_extra = false)
{
    return function($data) use ($coll, $err_on_extra) {
        $violations = [];

        foreach ($coll as $key => $param) {
            $flags_str = '';
            if (is_array($param)) {
                list($flags_str, $validator) = $param;
            }
            else {
                $validator = $param;
            }
            $flags = collection_parse_flags($flags_str);

            if (!array_key_exists($key, $data) && !$flags['optional']) {
                $violations[$key] = new Violation(
                    ViolationCodes::MISSING_FIELD,
                    [$key]
                );
                continue;
            }
            else if (!array_key_exists($key, $data) && $flags['optional']){
                continue;
            }

            $res = validate($validator, $data[$key]);

            if ($res) {
                $violations[$key] = $res;
            }
        }

        if ($err_on_extra) {
            $extra_keys = array_diff(array_keys($data), array_keys($coll));
            foreach ($extra_keys as $key) {
                $violations[$key] = new Violation(
                    ViolationCodes::EXTRA_FIELD,
                    [$key]
                );
            }
        }

        if ($violations) {
            return $violations;
        }

        return null;
    };
}

/**
 * takes a collection of validators and validates the value with each of the validators
 * one after the other.
 * @param $validators a traversable instance of validators to be applied to the value
 * @param bool $greedy if greedy then it will run all validators else it'll stop on first error
 */
function pipe($validators, $greedy = false)
{
    return function($value) use ($validators, $greedy) {
        $violations = [];
        foreach ($validators as $validator) {
            $res = validate($validator, $value);

            if ($res && !$greedy) {
                return $res;
            }
            else if ($res) {
                $violations[] = $res;
            }
        }

        if ($violations) {
            return $violations;
        }

        return null;
    };
}

/**
 * pipe greedy - pronounced pipe g. Defers execution to the pipe validator but as greedy
 * @param $validators a traversable of validators to be applied to the value
 */
function pipeg($validators)
{
    return pipe($validators, true);
}

/**
 * perform a transformation on the value before validating. this is useful
 * for slicing a collection of data into just a few fields for validating
 * or something like that
 */
function transform(callable $transformer, $validator)
{
    return function($value) use ($transformer, $validator)
    {
        return validate($validator, $transformer($value));
    };
}

/**
 * Always returns no error. If you want to see if a value exists in a collection,
 * this works great because it'll verify if the value actually is in the collection
 */
function stub()
{
    return function($value) {
        return;
    };
}

/**
 * Alias of stub
 */
function exists()
{
    return stub();
}

/**
 * Validates that a value is inside of an array of accpeted values
 */
function choice($accepted)
{
    return function($value) use ($accepted)
    {
        if (in_array($value, $accepted)) {
            return;
        }

        return new Violation(
            ViolationCodes::INVALID_CHOICE,
            [$accepted]
        );
    };
}

/**
 * Apply the validator for all values in an array
 */
function for_all($validator)
{
    return function($values) use ($validator) {
        foreach ($values as $val) {
            $res = $validator($val);
            if ($res) {
                return new Violation(
                    ViolationCodes::INVALID_COLLECTION,
                    [$res]
                );
            }
        }
    };
}

/**
 * This validator will just return whatever is passed on
 */
function mock($violation)
{
    return function($value) use ($violation) {
        return $violation;
    };
}
