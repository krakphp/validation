<?php

namespace Krak\Validation;

final class ViolationCodes
{
    const MISSING_FIELD = 'missing_field';
    const EXTRA_FIELD = 'extra_field';
    const INVALID_CHOICE = 'invalid_choice';
    const INVALID_COLLECTION = 'invalid_collection';
    const INVALID_TYPE = 'invalid_type';
    const NOT_INSTANCE_OF = 'not_instance_of';
    const NOT_TRAVERSABLE = 'not_traversable';
    const NOT_ITERABLE = 'not_iterable';
    const NOT_UNIQUE_ENTITY = 'not_unique_entity';
    const UNKOWN_ENTITY = 'unknown_entity';
    const FAILED_SYMFONY = 'failed_symfony';
    const NOT_GREATER_THAN = 'not_greater_than';
    const NOT_LESS_THAN = 'not_less_than';
    const NOT_GREATER_THAN_OR_EQUAL = 'not_greater_than_or_equal';
    const NOT_LESS_THAN_OR_EQUAL = 'not_less_than_or_equal';
    const NOT_EQUAL = 'not_equal';
}
