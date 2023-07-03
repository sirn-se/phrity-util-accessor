[![Build Status](https://github.com/sirn-se/phrity-util-accessor/actions/workflows/acceptance.yml/badge.svg)](https://github.com/sirn-se/phrity-util-accessor/actions)
[![Coverage Status](https://coveralls.io/repos/github/sirn-se/phrity-util-accessor/badge.svg?branch=main)](https://coveralls.io/github/sirn-se/phrity-util-accessor?branch=main)

# Introduction

Utility to handle access to a data set by using access paths.

## Installation

Install with [Composer](https://getcomposer.org/);
```
composer require phrity/util-accessor
```

# How to use

## Basic operation get() and has() methods

Any set of data (including arrays, objects, and scalar values) can be access using a path.

```php
use Phrity\Util\Accessor;

$subject = [
    'string-val' => 'A string',
    'assoc-array-val' => [
        'string-val' => 'Another string',
    ],
    'num-array-val' => [
        'a',
    ],
    'object-val' => (object)[
        'string-val' => 'Yet another string',
    ],
];

$accessor = new Accessor();

$accessor->get($subject, 'string-val'); // => "A string"
$accessor->get($subject, 'assoc-array-val'); // => ['string-val' => "Another string"]
$accessor->get($subject, 'assoc-array-val/string-val'); // => "Another string"
$accessor->get($subject, 'num-array-val/0'); // => "a"
$accessor->get($subject, 'object-val/string-val'); // => "Yet another string"

$accessor->has($subject, 'assoc-array-val/string-val'); // => true
$accessor->has($subject, 'assoc-array-val/non-exising'); // => false
```

## Using default return value for get() method

The get() method can also have default value specified, to be returned when path do not match the data set.
If not specified, null will be returned in these cases.

```php
use Phrity\Util\Accessor;

$subject = [
    'string-val' => 'A string',
];

$accessor = new Accessor();

$accessor->get($subject, 'non-existing'); // => null
$accessor->get($subject, 'non-existing', 'My default'); // => "My default"
```

## Specifying path separator

By default, path uses `/` as separator. Optionally, separator can be set in constructor.

```php
use Phrity\Util\Accessor;

$subject = [
    'object-val' => (object)[
        'string-val' => 'A string',
    ],
];

$accessor = new Accessor('.');

$accessor->get($subject, 'object-val.string-val'); // => "A string"
$accessor->has($subject, 'object-val.string-val'); // => true
```

## The PathAccessor

If multiple data sets should be accessed using the same path, the PathAccessor can be used instead.
The path is then specified on constructor, and then used on all calls to get() and has().

```php
use Phrity\Util\PathAccessor;

$subject_1 = [
    'object-val' => (object)[
        'string-val' => 'A string',
    ],
];
$subject_2 = [
    'object-val' => (object)[
        'string-val' => 'Another string',
    ],
];

$accessor = new PathAccessor('object-val/string-val');

$accessor->get($subject_1); // => "A string"
$accessor->get($subject_2); // => "Another string"
```

## The DataAccessor

If a data sets should be accessed using multiple paths, the DataAccessor can be used instead.
The data is then specified on constructor, and then used on all calls to get() and has().

```php
use Phrity\Util\DataAccessor;

$subject = [
    'object-val' => (object)[
        'string-val' => 'A string',
        'int-val' => 23,
    ],
];


$accessor = new DataAccessor(subject);

$accessor->get('object-val/string-val'); // => "A string"
$accessor->get('object-val/int-val'); // => 23
```


# Versions

| Version | PHP | |
| --- | --- | --- |
| `1.0` | `^7.4\|^8.0` | Initial version: get(), has() |
