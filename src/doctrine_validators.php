<?php

namespace Krak\Validation;

use Doctrine\Common\Persistence;

function doctrine_entity(Persistence\ObjectManager $em, $class_name, $alias = '')
{
    return function($value) use ($em, $class_name, $alias) {
        $entity = $em->find($class_name, $value);

        if (!$entity) {
            return new Violation(
                ViolationCodes::UNKOWN_ENTITY,
                [$class_name, $alias]
            );
        }
    };
}

function doctrine_unique_entity(Persistence\ObjectRepository $repo, $field, $alias = '')
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
