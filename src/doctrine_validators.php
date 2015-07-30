<?php

namespace Krak\Validation;

use Doctrine\Common\Persistence\ObjectRepository;

function doctrine_entity(ObjectRepository $repo, $field = 'id', $alias = '')
{
    return function($value) use ($repo, $field, $alias) {
        $entity = $repo->findOneBy([
            $field => $value,
        ]);

        if (!$entity) {
            return new Violation(
                ViolationCodes::ENTITY_NOT_FOUND,
                [$repo->getClassName(), $field, $alias, $value]
            );
        }
    };
}

function doctrine_unique_entity(ObjectRepository $repo, $field, $alias = '')
{
    return function($value) use ($repo, $field, $alias)
    {
        $entity = $repo->findOneBy([
            $field => $value,
        ]);

        if ($entity) {
            return new Violation(
                ViolationCodes::NOT_UNIQUE_ENTITY,
                [$repo->getClassName(), $field, $alias]
            );
        }
    };
}
