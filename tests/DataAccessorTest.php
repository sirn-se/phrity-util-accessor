<?php

declare(strict_types=1);

namespace Phrity\Util\Test;

use PHPUnit\Framework\TestCase;
use Phrity\Util\DataAccessor;

/**
 * DataAccessor test class.
 */
class DataAccessorTest extends TestCase
{
    public function testGet(): void
    {
        $subject = [
            'string-val' => 'A string',
            'object-val' => (object)[
                'string-val-2' => 'Yet another string',
            ],
        ];
        $accessor = new DataAccessor($subject);
        $result = $accessor->get('string-val', 'The default');
        $this->assertEquals('A string', $result);
        $result = $accessor->get('object-val/string-val-2', 'The default');
        $this->assertEquals('Yet another string', $result);
    }

    public function testHas(): void
    {
        $subject = [
            'string-val' => 'A string',
            'object-val' => (object)[
                'string-val-2' => 'Yet another string',
            ],
        ];
        $accessor = new DataAccessor($subject);
        $this->assertTrue($accessor->has('string-val', 'string-val'));
        $this->assertTrue($accessor->has('object-val/string-val-2', 'string-val'));
    }

    public function testObject(): void
    {
        $subject = new TestObject();
        $accessor = new DataAccessor($subject);

        $this->assertTrue($accessor->has(''));
        $this->assertInstanceOf(TestObject::class, $accessor->get(''));
        $this->assertTrue($accessor->has('public'));
        $this->assertEquals('public', $accessor->get('public'));
        $this->assertFalse($accessor->has('protected'));
        $this->assertNull($accessor->get('protected'));
        $this->assertFalse($accessor->has('private'));
        $this->assertNull($accessor->get('private'));
    }
}
