<?php

namespace Krak\Validation;

use ArrayIterator;
use IteratorAggregate;

use iter;

class ViolationCollection implements IteratorAggregate
{
    use ViolationParams;

    public $violations;

    public function __construct(array $violations) {
        $this->violations = $violations;
    }

    public function withViolations($violations) {
        $v = clone $this;
        $v->violations = is_array($violations) ? $violations : iter\toArray($violations);
        return $v;
    }

    public function format(FormatViolations $format = null) {
        $format = $format ?: $this->get('validation');
        if (!$format) {
            throw new \LogicException("Cannot format this ViolationCollection because no formatter was supplied.");
        }

        return $format->formatViolations($this);
    }

    public function formatMessages(Validation $validation = null) {
        $validation = $validation ?: $this->get('validation');
        if (!$validation) {
            throw new \LogicException("Cannot format this ViolationCollection messages because no validation instance was supplied.");
        }

        return $this->map(function($v) use ($validation) {
            if (!$v->get('formatted') && $v->has('message')) {
                return $v;
            }
            $message = $validation->getMessage($v);

            return $validation->formatMessage($v, $message)->with('formatted', true);
        });
    }

    public function toMessages() {
        return iter\toArray(iter\map(function($v) {
            return $v->get('message');
        }, $this));
    }

    public function find($key) {
        $parts = explode('.', $key);
        return $this->filter(function($v) use ($key, $parts) {
            if ($key == $v->code) {
                return true;
            }

            $attribute = $v->get('attribute');
            if (!$attribute) {
                return false;
            }

            $attribute_parts = explode('.', $attribute);
            $attribute_parts[] = $v->code;

            foreach ($parts as $i => $part) {
                if ($part == '*') {
                    continue;
                }
                if ($part != $attribute_parts[$i]) {
                    return false;
                }
            }

            return true;
        });
    }

    public function filter(callable $predicate) {
        $v = $this->flatten();
        return $v->withViolations(iter\filter($predicate, $v));
    }

    public function map(callable $predicate) {
        $v = $this->flatten();
        return $v->withViolations(iter\map($predicate, $v));
    }

    public function flatten() {
        if ($this->get('flattened')) {
            return $this;
        }

        $violations = iter\chain(...iter\map(function($v) {
            return $v->flatten();
        }, $this->violations));

        return $this->withViolations($violations)->with('flattened', true);
    }

    public function getIterator() {
        return new ArrayIterator($this->violations);
    }
}
