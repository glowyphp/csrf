<h1 align="center">Csrf Component</h1>
<p align="center">
Cross Site Request Forgery security component.
</p>
<p align="center">
<a href="https://github.com/atomastic/csrf/releases"><img alt="Version" src="https://img.shields.io/github/release/atomastic/csrf.svg?label=version&color=green"></a> <a href="https://github.com/atomastic/csrf"><img src="https://img.shields.io/badge/license-MIT-blue.svg?color=green" alt="License"></a> <a href="https://packagist.org/packages/atomastic/csrf"><img src="https://poser.pugx.org/atomastic/csrf/downloads" alt="Total downloads"></a> <img src="https://github.com/atomastic/csrf/workflows/Static%20Analysis/badge.svg?branch=dev"> <img src="https://github.com/atomastic/csrf/workflows/Tests/badge.svg"> <a href="https://app.codacy.com/gh/atomastic/csrf?utm_source=github.com&utm_medium=referral&utm_content=atomastic/csrf&utm_campaign=Badge_Grade"><img src="https://app.codacy.com/project/badge/Grade/97d21d0ac6024f78ba535255c80422e6"></a> <a href="https://codeclimate.com/github/atomastic/csrf/maintainability"><img src="https://api.codeclimate.com/v1/badges/b3c645f65e77a79e4a19/maintainability" /></a>
</p>

<br>

* [Installation](#installation)
* [Usage](#usage)
* [Methods](#methods)
* [Tests](#tests)
* [License](#license)

### Installation

#### With [Composer](https://getcomposer.org)

```
composer require atomastic/csrf
```

### Usage

```php
use Atomastic\Csrf\Csrf;

// Start PHP session
session_start();

/**
 * Create the csrf object.
 *
 * @param string  $tokenNamePrefix  Prefix for CSRF token name.
 * @param string  $tokenValuePrefix Prefix for CSRF token value.
 * @param int     $strength         Strength.
 *
 * @throws CsrfException
 */
$csrf = new Csrf('__csrf_name',
                 '__csrf_value',
                 32);
```

### Methods

| Method | Description |
|---|---|
| <a href="#csrf_getTokenName">`getTokenName()`</a> | Get token name. |
| <a href="#csrf_getTokenValue">`getTokenValue()`</a> | Get token value. |
| <a href="#csrf_isValid">`isValid()`</a> | Checks whether an incoming CSRF token name and value is valid. |

#### Methods Details

##### <a name="csrf_getTokenName"></a> Method: `getTokenName()`

```php
/**
 * Get token name.
 *
 * @return string
 */
public function getTokenName(): string
```

##### Example

```html
<input type="hidden" name="<?php echo $csrf->getTokenName(); ?>" value="<?php echo $csrf->getTokenValue(); ?>"></input>
```

##### <a name="csrf_getTokenValue"></a> Method: `getTokenValue()`

```php
/**
 * Get token value.
 *
 * @return string
 */
public function getTokenValue(): string
```

##### Example

```html
<input type="hidden" name="<?php echo $csrf->getTokenName(); ?>" value="<?php echo $csrf->getTokenValue(); ?>" />
```

##### <a name="csrf_isValid"></a> Method: `isValid()`

```php
/**
 * Checks whether an incoming CSRF token name and value is valid.
 *
 * @param string $name  The incoming token name.
 * @param string $value The incoming token value.
 *
 * @return bool True if valid, false if not.
 */
public function isValid(string $name, string $value): bool
```

##### Example

```php
if (! $csrf->isValid($_POST[$csrf->getTokenName()])) {
    echo "This looks like a cross-site request forgery.";
} else {
    echo "This looks like a valid request.";
}
```

### Tests

Run tests

```
./vendor/bin/pest
```

### License
[The MIT License (MIT)](https://github.com/atomastic/csrf/blob/master/LICENSE.txt)
Copyright (c) 2021 [Sergey Romanenko](https://github.com/Awilum)
