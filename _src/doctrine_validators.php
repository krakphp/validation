<?php

namespace Krak\Validation;

use Doctrine\Common\Persistence\ObjectRepository;

function doctrine_entity(ObjectRepository $repo, $field = 'id') {
    return function($value) use ($repo, $field) {
        $entity = $repo->findOneBy([
            $field => $value,
        ]);

        if (!$entity) {
            return violate(
                ViolationCodes::ENTITY_NOT_FOUND,
                Params::name($repo->getClassName())
            );
        }
    };
}

function doctrine_entities(ObjectRepository $repo, $field = 'id') {
    return function($values) use ($repo, $field) {
        $entities = $repo->findBy([
            $field => $values,
        ]);

        if (\count($entities) !== \count($values)) {
            return violate(
                ViolationCodes::ENTITIES_NOT_FOUND,
                Params::name($repo->getClassName())
            );
        }
    };
}

function doctrine_unique_entity(ObjectRepository $repo, $field) {
    return function($value) use ($repo, $field, $alias) {
        $entity = $repo->findOneBy([
            $field => $value,
        ]);

        if ($entity) {
            return violate(
                ViolationCodes::NOT_UNIQUE_ENTITY,
                Params::name($repo->getClassName())
            );
        }
    };
}
