<?php

declare(strict_types=1);

namespace Phrity\Util\Test;

use Stringable;

/**
 * Stringable test class.
 */
class TestStringableObject implements Stringable
{
    public string $public = 'public';
    protected string $protected = 'protected';
    /* @phpstan-ignore property.onlyWritten */
    private string $private = 'private';
    public static string $spublic = 'static-public';
    protected static string $sprotected = 'static-protected';
    /* @phpstan-ignore property.onlyWritten */
    private static string $sprivate = 'static-private';

    public function __toString(): string
    {
        return 'Stringable test class';
    }
}
