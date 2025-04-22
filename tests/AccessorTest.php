<?php

declare(strict_types=1);

namespace Phrity\Util\Test;

use PHPUnit\Framework\TestCase;
use Phrity\Util\Accessor;
use Phrity\Util\Transformer\{
    BasicTypeConverter,
    Type,
};

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

    public function testCoercion(): void
    {
        $accessor = new Accessor();
        $subject = [
            'string-val' => 'A string',
            'assoc-array-val' => [
                'string-val-2' => 'Another string',
                'null-val-2' => null,
            ],
            'object-val' => (object)[
                'string-val-3' => 'Yet another string',
                'null-val-3' => null,
            ],
        ];
        $result = $accessor->get($subject, 'string-val', coerce: Type::ARRAY);
        $this->assertEquals(['A string'], $result);
        $result = $accessor->get($subject, 'assoc-array-val', coerce: Type::OBJECT);
        $this->assertEquals((object)[
            'string-val-2' => 'Another string',
            'null-val-2' => null,
        ], $result);
        $result = $accessor->get($subject, 'object-val', coerce: Type::ARRAY);
        $this->assertEquals([
            'string-val-3' => 'Yet another string',
            'null-val-3' => null,
        ], $result);
        $result = $accessor->get($subject, 'object-val', coerce: Type::STRING);
        $this->assertEquals('stdClass', $result);
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

    public function testSet(): void
    {
        $accessor = new Accessor();

        $subject = ['string-val' => 'A string'];
        $result = $accessor->set($subject, '', [1, 2, 3]);
        $this->assertEquals([1, 2, 3], $result);
        $result = $accessor->set($subject, 'string-val', 'Changed string');
        $this->assertEquals(['string-val' => 'Changed string'], $result);
        $result = $accessor->set($subject, 'non-existing', 'New string');
        $this->assertEquals(['string-val' => 'A string', 'non-existing' => 'New string'], $result);

        $subject = ['assoc-array-val' => ['string-val' => 'A string', 'null-val' => null]];
        $result = $accessor->set($subject, 'assoc-array-val/string-val', 'Changed string');
        $this->assertEquals(['assoc-array-val' => ['string-val' => 'Changed string', 'null-val' => null]], $result);
        $result = $accessor->set($subject, 'assoc-array-val/non-existing', 'New string');
        $this->assertEquals([
            'assoc-array-val' => ['string-val' => 'A string', 'non-existing' => 'New string', 'null-val' => null]
        ], $result);

        $subject = ['object-val' => (object)['string-val' => 'A string', 'null-val' => null]];
        $result = $accessor->set($subject, 'object-val/string-val', 'Changed string');
        $this->assertEquals(['object-val' => (object)['string-val' => 'Changed string', 'null-val' => null]], $result);
        $result = $accessor->set($subject, 'object-val/non-existing', 'New string');
        $this->assertEquals([
            'object-val' => (object)['string-val' => 'A string', 'non-existing' => 'New string', 'null-val' => null]
        ], $result);

        $subject = [];
        $result = $accessor->set($subject, 'a/b/c', ['d' => ['e' => ['f' => 'ok']]]);
        $this->assertEquals(['a' => ['b' => ['c' => ['d' => ['e' => ['f' => 'ok']]]]]], $result);

        $subject = ['a' => 'A string'];
        $result = $accessor->set($subject, 'a/b', 'Changed type');
        $this->assertEquals(['a' => ['b' => 'Changed type']], $result);
    }

    public function testObject(): void
    {
        $subject = new TestObject();
        $accessor = new Accessor();
        $this->assertTrue($accessor->has($subject, ''));
        $this->assertInstanceOf(TestObject::class, $accessor->get($subject, ''));
        $this->assertTrue($accessor->has($subject, 'public'));
        $this->assertEquals('public', $accessor->get($subject, 'public'));
        $this->assertFalse($accessor->has($subject, 'protected'));
        $this->assertNull($accessor->get($subject, 'protected'));
        $this->assertFalse($accessor->has($subject, 'private'));
        $this->assertNull($accessor->get($subject, 'private'));

        $clone = $accessor->set($subject, 'public', 'Changed public');
        $this->assertNotSame($subject, $clone);
        $this->assertEquals('Changed public', $clone->public);
    }
}
