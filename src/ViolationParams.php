<?php

namespace Krak\Validation;

trait ViolationParams {
    public $params;

    public function with($key, $value) {
        $v = clone $this;
        $v->params[$key] = $value;

        return $v;
    }

    public function without($key) {
        $v = clone $this;
        unset($v->params[$key]);
        return $v;
    }

    public function withAddedParams(array $params) {
        $v = clone $this;
        $v->params = array_merge($v->params, $params);
        return $v;
    }

    public function withParams(array $params) {
        $v = clone $this;
        $v->params = $params;
        return $v;
    }

    public function has($key) {
        if (!$this->params) {
            return false;
        }
        return array_key_exists($key, $this->params);
    }

    public function get($key, $default = null) {
        if (!$this->has($key)) {
            return $default;
        }

        return $this->params[$key];
    }
}
