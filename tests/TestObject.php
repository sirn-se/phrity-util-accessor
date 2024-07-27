<?php

declare(strict_types=1);

namespace Phrity\Util\Test;

/**
 * Test class.
 */
class TestObject
{
    public string $public = 'public';
    protected string $protected = 'protected';
    private string $private = 'private';
    public static string $spublic = 'static-public';
    protected static string $sprotected = 'static-protected';
    private static string $sprivate = 'static-private';
}
