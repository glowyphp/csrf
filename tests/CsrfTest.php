<?php

declare(strict_types=1);

use Atomastic\Csrf\Csrf;

beforeEach(function (): void {
    session_start();
    $_SESSION   = [];
    $this->csrf = new Csrf();
});

afterEach(function (): void {
    unset($_SESSION);
    session_destroy();
});

test('test instance', function (): void {
    $this->assertInstanceOf(Csrf::class, $this->csrf);
});

test('test getTokenName()', function (): void {
    $this->assertTrue(is_string($this->csrf->getTokenName()));
});

test('test getTokenValue()', function (): void {
    $this->assertTrue(is_string($this->csrf->getTokenValue()));
});

test('test isValid()', function (): void {
    $post = [$this->csrf->getTokenName() => $this->csrf->getTokenValue()];

    $this->assertTrue($this->csrf->isValid($post[$this->csrf->getTokenName()]));
    $this->assertFalse($this->csrf->isValid('bar'));
});
