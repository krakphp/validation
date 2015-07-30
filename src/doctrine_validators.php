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

function doctrine_entities(ObjectRepository $repo, $field = 'id', $alias = '')
{
    return function($values) use ($repo, $field, $alias) {
        $entities = $repo->findBy([
            $field => $values,
        ]);

        if (\count($entities) !== \count($values)) {
            return new Violation(
                ViolationCodes::ENTITIES_NOT_FOUND,
                [$repo->getClassName(), $field, $alias, $values]
            );
        }
    };
}

function doctrine_unique_entity(ObjectRepository $repo, $field, $alias = '')
{
    return function($value) use ($repo, $field, $alias) {
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
