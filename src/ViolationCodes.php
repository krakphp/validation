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
}
