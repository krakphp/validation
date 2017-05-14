<?php

describe('Krak Validation', function() {
    describe('Validators', function() {
        require_once __DIR__ . '/validators.php';
    });
    describe('Violation', function() {
        require_once __DIR__ . '/violation.php';
    });
    describe('ViolationCollection', function() {
        require_once __DIR__ . '/violation-collection.php';
    });
});
