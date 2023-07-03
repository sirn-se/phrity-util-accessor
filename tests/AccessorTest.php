<?php

declare(strict_types=1);

namespace Phrity\Util\Test;

use PHPUnit\Framework\TestCase;
use Phrity\Util\Accessor;

/**
 * Accessor test class.
 */
class AccessorTest extends TestCase
{
    public function testGet(): void
    {
        $accessor = new Accessor();
        $subject = [
            'string-val' => 'A string',
            'null-val' => null,
            'assoc-array-val' => [
                'string-val-2' => 'Another string',
                'null-val-2' => null,
            ],
            'num-array-val' => [
                'a',
                'b',
                'c',
            ],
            'object-val' => (object)[
                'string-val-3' => 'Yet another string',
                'null-val-3' => null,
            ],
        ];
        $result = $accessor->get($subject, 'string-val', 'The default');
        $this->assertEquals('A string', $result);
        $result = $accessor->get($subject, 'null-val', 'The default');
        $this->assertNull($result);
        $result = $accessor->get($subject, 'non-existing', 'The default');
        $this->assertEquals('The default', $result);
        $result = $accessor->get($subject, '', 'The default');
        $this->assertEquals($subject, $result);
        $result = $accessor->get($subject, 'assoc-array-val', 'The default');
        $this->assertEquals([
            'string-val-2' => 'Another string',
            'null-val-2' => null,
        ], $result);
        $result = $accessor->get($subject, 'assoc-array-val/string-val-2', 'The default');
        $this->assertEquals('Another string', $result);
        $result = $accessor->get($subject, 'assoc-array-val/null-val-2', 'The default');
        $this->assertNull($result);
        $result = $accessor->get($subject, 'num-array-val', 'The default');
        $this->assertEquals(['a', 'b', 'c'], $result);
        $result = $accessor->get($subject, 'num-array-val/0', 'The default');
        $this->assertEquals('a', $result);
        $result = $accessor->get($subject, 'num-array-val/1', 'The default');
        $this->assertEquals('b', $result);
        $result = $accessor->get($subject, 'num-array-val/2', 'The default');
        $this->assertEquals('c', $result);
        $result = $accessor->get($subject, 'object-val', 'The default');
        $this->assertEquals((object)[
            'string-val-3' => 'Yet another string',
            'null-val-3' => null,
        ], $result);
        $result = $accessor->get($subject, 'object-val/string-val-3', 'The default');
        $this->assertEquals('Yet another string', $result);
        $result = $accessor->get($subject, 'object-val/non-existing', 'The default');
        $this->assertEquals('The default', $result);
        $result = $accessor->get($subject, 'object-val/null-val-3', 'The default');
        $this->assertNull($result);
        $result = $accessor->get(null, 'non-existing', 'The default');
        $this->assertEquals('The default', $result);
    }

    public function testHas(): void
    {
        $accessor = new Accessor();
        $subject = [
            'string-val' => 'A string',
            'null-val' => null,
            'assoc-array-val' => [
                'string-val-2' => 'Another string',
                'null-val-2' => null,
            ],
            'num-array-val' => [
                'a',
                'b',
                'c',
            ],
            'object-val' => (object)[
                'string-val-3' => 'Yet another string',
                'null-val-3' => null,
            ],
        ];
        $this->assertTrue($accessor->has($subject, ''));
        $this->assertTrue($accessor->has($subject, 'string-val'));
        $this->assertTrue($accessor->has($subject, 'assoc-array-val/string-val-2'));
        $this->assertTrue($accessor->has($subject, 'num-array-val/0'));
        $this->assertTrue($accessor->has($subject, 'object-val/string-val-3'));
        $this->assertFalse($accessor->has($subject, 'non-existing'));
        $this->assertFalse($accessor->has($subject, 'assoc-array-val/non-existing'));
        $this->assertFalse($accessor->has($subject, 'object-val/non-existing'));
        $this->assertFalse($accessor->has($subject, 'num-array-val/3'));
        $this->assertFalse($accessor->has(null, 'non-existing'));
    }
}
