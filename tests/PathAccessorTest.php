<?php

declare(strict_types=1);

namespace Phrity\Util\Test;

use PHPUnit\Framework\TestCase;
use Phrity\Util\PathAccessor;

/**
 * PathAccessor test class.
 */
class PathAccessorTest extends TestCase
{
    public function testGet(): void
    {
        $accessor = new PathAccessor('string-val');
        $subject_1 = ['string-val' => 'A string'];
        $result = $accessor->get($subject_1, 'string-val', 'The default');
        $this->assertEquals('A string', $result);
        $subject_2 = ['string-val' => 'Another string'];
        $result = $accessor->get($subject_2, 'string-val', 'The default');
        $this->assertEquals('Another string', $result);
    }

    public function testHas(): void
    {
        $accessor = new PathAccessor('string-val');
        $subject_1 = ['string-val' => 'A string'];
        $this->assertTrue($accessor->has($subject_1, 'string-val'));
        $subject_2 = ['string-val' => 'Another string'];
        $this->assertTrue($accessor->has($subject_2, 'string-val'));
    }

    public function testSet(): void
    {
        $accessor = new PathAccessor('string-val');
        $subject = ['string-val' => 'A string', 'null-val' => null];

        $result = $accessor->set($subject, 'New string');
        $this->assertEquals(['string-val' => 'New string', 'null-val' => null], $result);
    }

    public function testObject(): void
    {
        $subject = new TestObject();
        $accessor = new PathAccessor('public');

        $this->assertTrue($accessor->has($subject));
        $this->assertEquals('public', $accessor->get($subject));

        $clone = $accessor->set($subject, 'Changed public');
        $this->assertNotSame($subject, $clone);
        $this->assertEquals('Changed public', $clone->public);
    }
}
