<p align="center"><img src="docs/logotype.png" alt="Phrity Util Accessor" width="100%"></p>

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

## Basic operation `get()` and `has()` methods

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

## Using default return value for `get()` method

The `get()` method can also have default value specified, to be returned when path do not match the data set.
If not specified, `null` will be returned in these cases.

```php
use Phrity\Util\Accessor;

$subject = [
    'string-val' => 'A string',
];

$accessor = new Accessor();

$accessor->get($subject, 'non-existing'); // => null
$accessor->get($subject, 'non-existing', 'My default'); // => "My default"
```

## Using type coercion with `get()` method

The `get()` method may coerce returned value into specified type.

```php
use Phrity\Util\Accessor;
use Phrity\Util\Transformer\Type;

$subject = [
    'float-val' => 12.34,
    'assoc-array-val' => [
        'string-val' => 'Another string',
    ],
];

$accessor = new Accessor();

$accessor->get($subject, 'float-val', coerce: Type::STRING); // Return float as string
$accessor->get($subject, 'assoc-array-val', coerce: Type::OBJECT); // Return array as object
```

By default the Accessor will use basic conversion.
For more options, [Transformers](https://github.com/sirn-se/phrity-util-transformer) can be specified on Accessors.

```php
use Phrity\Util\Accessor;
use Phrity\Util\Transformer\{
    FirstMatchResolver,
    EnumConverter,
    StringableConverter,
    BasicTypeConverter,
};

$transformer = new FirstMatchResolver([
    new EnumConverter(),
    new StringableConverter(),
    new BasicTypeConverter(),
]);
$accessor = new Accessor(transformer: $transformer);
```


## The `set()` method

The `set()` method add or replace value in data set as specified by path.
Note that this operation do not merge values, but set explicitly.

Depending on scope, it may not be possible to use `set()` on class properties.
In this case an `AccessorException` will be thrown.

```php
use Phrity\Util\Accessor;

$subject = [
    'string-val' => 'A string',
];

$accessor = new Accessor();

$subject = $accessor->set($subject, 'string-val', 'Replaced value');
$subject = $accessor->set($subject, 'non-existing', 'Added value');
// $subject => ['string-val' => 'Replaced value', 'non-existing' => 'Added value']
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
The path is then specified on constructor, and then used on all calls to `get()`, `has()` and `set()`.

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
$subject_1 = $accessor->set($subject_1, 'Replaced value');
$subject_2 = $accessor->set($subject_2, 'Replaced value');
```

## The DataAccessor

If a data sets should be accessed using multiple paths, the DataAccessor can be used instead.
The data is then specified on constructor, and then used on all calls to `get()`, `has()` and `set()`.

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
$accessor->set('object-val/string-val', 'Replaced string');
$accessor->set('object-val/int-val', 48);
```

## I want to incorporate this in my own class

Sure, no problem. Just use the `AccessorTrait` in your class, and call the available worker methods.
The internal, recursive worker methods takes path as an array of path segments.
There is also a helper to extract array path from a string path.

```php

use Phrity\Util\AccessorTrait;

class MyClass
{
    use AccessorTrait;

    public function doThings(): void
    {
        $my_data = ['array-val' => ['string-val' => 'A string']];
        $exists = $this->accessorHas($my_data, ['array-val', 'string-val']);
        $something = $this->accessorGet($my_data, ['array-val', 'string-val'], 'My default');

        $my_path = $this->accessorParsePath('array-val#string-val', '#');
        $exists = $this->accessorHas($my_data, $my_path);
        $something = $this->accessorGet($my_data, $my_path, 'My default');
        $modified = $this->accessorSet($my_data, $my_path, 'My new value');
    }
}
```

# Versions

| Version | PHP | |
| --- | --- | --- |
| `1.3` | `^8.1` | Type coercion using Transformers |
| `1.2` | `^8.1` | DataAccessor implements JsonSerializable |
| `1.1` | `^8.0` | `set()` method |
| `1.0` | `^7.4\|^8.0` | Initial version: `get()`, `has()` methods |
