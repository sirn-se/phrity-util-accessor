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
}
