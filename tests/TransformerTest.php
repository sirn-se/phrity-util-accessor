<?php

declare(strict_types=1);

namespace Phrity\Util\Test;

use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\TestCase;
use Phrity\Util\Transformer\{
    BasicTypeConverter,
    EnumConverter,
    FirstMatchResolver,
    ReadableConverter,
    RecursionResolver,
    StringableConverter,
    ThrowableConverter,
    TransformerException,
    Type,
};

/**
 * Transformer test class.
 */
class TransformerTest extends TestCase
{
    public function testBasicTypeConverter(): void
    {
        $transformer = new BasicTypeConverter();

        $subject = 'A string'; // String
        $this->assertSame('A string', $transformer->transform($subject));
        $this->assertSame(['A string'], $transformer->transform($subject, Type::ARRAY));
        $this->assertSame(true, $transformer->transform($subject, Type::BOOLEAN));
        $this->assertSame(0, $transformer->transform($subject, Type::INTEGER));
        $this->assertSame(null, $transformer->transform($subject, Type::NULL));
        $this->assertSame(0.0, $transformer->transform($subject, Type::NUMBER));
        $this->assertEquals((object)['scalar' => 'A string'], $transformer->transform($subject, Type::OBJECT));
        $this->assertSame('A string', $transformer->transform($subject, Type::STRING));

        $subject = 123.456; // Float
        $this->assertSame(123.456, $transformer->transform($subject));
        $this->assertSame([123.456], $transformer->transform($subject, Type::ARRAY));
        $this->assertSame(true, $transformer->transform($subject, Type::BOOLEAN));
        $this->assertSame(123, $transformer->transform($subject, Type::INTEGER));
        $this->assertSame(null, $transformer->transform($subject, Type::NULL));
        $this->assertSame(123.456, $transformer->transform($subject, Type::NUMBER));
        $this->assertEquals((object)['scalar' => 123.456], $transformer->transform($subject, Type::OBJECT));
        $this->assertSame('123.456', $transformer->transform($subject, Type::STRING));

        $subject = 789; // Int
        $this->assertSame(789, $transformer->transform($subject));
        $this->assertSame([789], $transformer->transform($subject, Type::ARRAY));
        $this->assertSame(true, $transformer->transform($subject, Type::BOOLEAN));
        $this->assertSame(789, $transformer->transform($subject, Type::INTEGER));
        $this->assertSame(null, $transformer->transform($subject, Type::NULL));
        $this->assertSame(789.0, $transformer->transform($subject, Type::NUMBER));
        $this->assertEquals((object)['scalar' => 789], $transformer->transform($subject, Type::OBJECT));
        $this->assertSame('789', $transformer->transform($subject, Type::STRING));

        $subject = true; // Bool (true)
        $this->assertSame(true, $transformer->transform($subject));
        $this->assertSame([true], $transformer->transform($subject, Type::ARRAY));
        $this->assertSame(true, $transformer->transform($subject, Type::BOOLEAN));
        $this->assertSame(1, $transformer->transform($subject, Type::INTEGER));
        $this->assertSame(null, $transformer->transform($subject, Type::NULL));
        $this->assertSame(1.0, $transformer->transform($subject, Type::NUMBER));
        $this->assertEquals((object)['scalar' => true], $transformer->transform($subject, Type::OBJECT));
        $this->assertSame('1', $transformer->transform($subject, Type::STRING));

        $subject = false; // Bool (false)
        $this->assertSame(false, $transformer->transform($subject));
        $this->assertSame([false], $transformer->transform($subject, Type::ARRAY));
        $this->assertSame(false, $transformer->transform($subject, Type::BOOLEAN));
        $this->assertSame(0, $transformer->transform($subject, Type::INTEGER));
        $this->assertSame(null, $transformer->transform($subject, Type::NULL));
        $this->assertSame(0.0, $transformer->transform($subject, Type::NUMBER));
        $this->assertEquals((object)['scalar' => false], $transformer->transform($subject, Type::OBJECT));
        $this->assertSame('', $transformer->transform($subject, Type::STRING));

        $subject = null; // Null
        $this->assertSame(null, $transformer->transform($subject));
        $this->assertSame([], $transformer->transform($subject, Type::ARRAY));
        $this->assertSame(false, $transformer->transform($subject, Type::BOOLEAN));
        $this->assertSame(0, $transformer->transform($subject, Type::INTEGER));
        $this->assertSame(null, $transformer->transform($subject, Type::NULL));
        $this->assertSame(0.0, $transformer->transform($subject, Type::NUMBER));
        $this->assertEquals((object)[], $transformer->transform($subject, Type::OBJECT));
        $this->assertSame('', $transformer->transform($subject, Type::STRING));

        $subject = [1, 'a']; // Array (non-empty)
        $this->assertSame([1, 'a'], $transformer->transform($subject));
        $this->assertSame([1, 'a'], $transformer->transform($subject, Type::ARRAY));
        $this->assertSame(true, $transformer->transform($subject, Type::BOOLEAN));
        $this->assertSame(1, $transformer->transform($subject, Type::INTEGER));
        $this->assertSame(null, $transformer->transform($subject, Type::NULL));
        $this->assertSame(1.0, $transformer->transform($subject, Type::NUMBER));
        $this->assertEquals((object)[0 => 1, 1 => 'a'], $transformer->transform($subject, Type::OBJECT));
        $this->assertSame('array', $transformer->transform($subject, Type::STRING));

        $subject = []; // Array (empty)
        $this->assertSame([], $transformer->transform($subject));
        $this->assertSame([], $transformer->transform($subject, Type::ARRAY));
        $this->assertSame(false, $transformer->transform($subject, Type::BOOLEAN));
        $this->assertSame(0, $transformer->transform($subject, Type::INTEGER));
        $this->assertSame(null, $transformer->transform($subject, Type::NULL));
        $this->assertSame(0.0, $transformer->transform($subject, Type::NUMBER));
        $this->assertEquals((object)[], $transformer->transform($subject, Type::OBJECT));
        $this->assertSame('array', $transformer->transform($subject, Type::STRING));

        $subject = (object)['a' => 1, 'b' => 2]; // Object (stdClass)
        $this->assertEquals((object)['a' => 1, 'b' => 2], $transformer->transform($subject));
        $this->assertSame(['a' => 1, 'b' => 2], $transformer->transform($subject, Type::ARRAY));
        $this->assertSame(true, $transformer->transform($subject, Type::BOOLEAN));
        $this->assertSame(1, $transformer->transform($subject, Type::INTEGER));
        $this->assertSame(null, $transformer->transform($subject, Type::NULL));
        $this->assertSame(1.0, $transformer->transform($subject, Type::NUMBER));
        $this->assertEquals((object)['a' => 1, 'b' => 2], $transformer->transform($subject, Type::OBJECT));
        $this->assertSame('stdClass', $transformer->transform($subject, Type::STRING));

        $subject = new TestObject(); // Object (class)
        $this->assertEquals((object)['public' => 'public'], $transformer->transform($subject));
        $this->assertSame(['public' => 'public'], $transformer->transform($subject, Type::ARRAY));
        $this->assertSame(true, $transformer->transform($subject, Type::BOOLEAN));
        $this->assertSame(1, $transformer->transform($subject, Type::INTEGER));
        $this->assertSame(null, $transformer->transform($subject, Type::NULL));
        $this->assertSame(1.0, $transformer->transform($subject, Type::NUMBER));
        $this->assertEquals((object)['public' => 'public'], $transformer->transform($subject, Type::OBJECT));
        $this->assertSame('Phrity\Util\Test\TestObject', $transformer->transform($subject, Type::STRING));

        $subject = TestBasicEnum::Yes;
        $this->assertEquals((object)['name' => 'Yes'], $transformer->transform($subject));
        $this->assertSame(['name' => 'Yes'], $transformer->transform($subject, Type::ARRAY));
        $this->assertSame(true, $transformer->transform($subject, Type::BOOLEAN));
        $this->assertSame(1, $transformer->transform($subject, Type::INTEGER));
        $this->assertSame(null, $transformer->transform($subject, Type::NULL));
        $this->assertSame(1.0, $transformer->transform($subject, Type::NUMBER));
        $this->assertEquals((object)['name' => 'Yes'], $transformer->transform($subject, Type::OBJECT));
        $this->assertSame('Phrity\Util\Test\TestBasicEnum', $transformer->transform($subject, Type::STRING));

        $subject = TestBackedEnum::Yes;
        $this->assertEquals((object)['name' => 'Yes', 'value' => 'Jajemen'], $transformer->transform($subject));
        $this->assertSame(['name' => 'Yes', 'value' => 'Jajemen'], $transformer->transform($subject, Type::ARRAY));
        $this->assertSame(true, $transformer->transform($subject, Type::BOOLEAN));
        $this->assertSame(1, $transformer->transform($subject, Type::INTEGER));
        $this->assertSame(null, $transformer->transform($subject, Type::NULL));
        $this->assertSame(1.0, $transformer->transform($subject, Type::NUMBER));
        $this->assertEquals(
            (object)['name' => 'Yes', 'value' => 'Jajemen'],
            $transformer->transform($subject, Type::OBJECT)
        );
        $this->assertSame('Phrity\Util\Test\TestBackedEnum', $transformer->transform($subject, Type::STRING));

        $subject = new LogicException('Error message', 123);
        $this->assertEquals((object)[], $transformer->transform($subject));
        $this->assertSame([], $transformer->transform($subject, Type::ARRAY));
        $this->assertSame(true, $transformer->transform($subject, Type::BOOLEAN));
        $this->assertSame(1, $transformer->transform($subject, Type::INTEGER));
        $this->assertSame(null, $transformer->transform($subject, Type::NULL));
        $this->assertSame(1.0, $transformer->transform($subject, Type::NUMBER));
        $this->assertEquals((object)[], $transformer->transform($subject, Type::OBJECT));
        $this->assertSame('LogicException', $transformer->transform($subject, Type::STRING));

        $subject = gzopen(sys_get_temp_dir() . '/test-temp.gz', 'w'); // Respurce
        $this->assertEquals('resource (stream)', $transformer->transform($subject));
        $this->assertSame(['resource (stream)'], $transformer->transform($subject, Type::ARRAY));
        $this->assertSame(true, $transformer->transform($subject, Type::BOOLEAN));
        $this->assertSame(0, $transformer->transform($subject, Type::INTEGER));
        $this->assertSame(null, $transformer->transform($subject, Type::NULL));
        $this->assertSame(0.0, $transformer->transform($subject, Type::NUMBER));
        $this->assertEquals((object)['scalar' => 'resource (stream)'], $transformer->transform($subject, Type::OBJECT));
        $this->assertSame('resource (stream)', $transformer->transform($subject, Type::STRING));
    }

    public function testBasicTypeConverterWithMap(): void
    {
        $transformer = new BasicTypeConverter([
            Type::ARRAY => Type::OBJECT,
            Type::OBJECT => Type::ARRAY,
            Type::BOOLEAN => Type::STRING,
            Type::INTEGER => Type::STRING,
            Type::NULL => Type::STRING,
            Type::NUMBER => Type::STRING,
            Type::STRING => Type::BOOLEAN,
        ]);
        $this->assertSame(true, $transformer->transform('A string'));
        $this->assertSame('123.456', $transformer->transform(123.456));
        $this->assertSame('789', $transformer->transform(789));
        $this->assertSame('1', $transformer->transform(true));
        $this->assertSame('', $transformer->transform(null));
        $this->assertEquals((object)[1, 'a'], $transformer->transform([1, 'a']));
        $this->assertEquals(['a' => 1, 'b' => 2], $transformer->transform((object)['a' => 1, 'b' => 2]));
    }

    public function testBasicTypeConverterUnsupportedType(): void
    {
        $transformer = new BasicTypeConverter();
        $subject = 'A string';
        $this->expectException(TransformerException::class);
        $this->expectExceptionMessage("Converting 'string' to 'Invalid type' is not supported.");
        $transformer->transform($subject, 'Invalid type');
    }

    public function testReadableConverter(): void
    {
        $transformer = new ReadableConverter();
        $this->assertSame('true', $transformer->transform(true, Type::STRING));
        $this->assertSame('false', $transformer->transform(false, Type::STRING));
        $this->assertSame('null', $transformer->transform(null, Type::STRING));
        $this->assertFalse($transformer->canTransform(true));
        $this->assertFalse($transformer->canTransform('A string', Type::STRING));
    }

    public function testReadableConverterUsingDefault(): void
    {
        $transformer = new ReadableConverter(perDefault: true);
        $this->assertSame('true', $transformer->transform(true));
        $this->assertSame('false', $transformer->transform(false));
        $this->assertSame('null', $transformer->transform(null));
        $this->assertFalse($transformer->canTransform(true, Type::ARRAY));
        $this->assertFalse($transformer->canTransform('A string'));
    }

    public function testReadableConverterUnsupported(): void
    {
        $transformer = new ReadableConverter();
        $this->expectException(TransformerException::class);
        $this->expectExceptionMessage("Creating readable for 'string' is not supported.");
        $transformer->transform('A string');
    }

    public function testStringableConverter(): void
    {
        $transformer = new StringableConverter();
        $this->assertSame('Stringable test class', $transformer->transform(new TestStringableObject(), Type::STRING));
        $this->assertFalse($transformer->canTransform(new TestStringableObject()));
        $this->assertFalse($transformer->canTransform(new TestObject(), Type::STRING));
    }

    public function testStringableConverterUsingDefault(): void
    {
        $transformer = new StringableConverter(perDefault: true);
        $this->assertSame('Stringable test class', $transformer->transform(new TestStringableObject()));
        $this->assertFalse($transformer->canTransform(new TestObject(), Type::STRING));
        $this->assertFalse($transformer->canTransform(new TestStringableObject(), Type::ARRAY));
    }

    public function testStringableConverterUnsupported(): void
    {
        $transformer = new StringableConverter();
        $this->expectException(TransformerException::class);
        $this->expectExceptionMessage("Creating stringable for 'string' is not supported.");
        $transformer->transform('A string');
    }

    public function testEnumConverter(): void
    {
        $transformer = new EnumConverter();
        $this->assertSame('Yes', $transformer->transform(TestBasicEnum::Yes, Type::STRING));
        $this->assertSame('No', $transformer->transform(TestBackedEnum::No, Type::STRING));
        $this->assertFalse($transformer->canTransform(TestBasicEnum::Yes));
        $this->assertFalse($transformer->canTransform('A string', Type::STRING));
    }

    public function testEnumConverterUsingDefault(): void
    {
        $transformer = new EnumConverter(perDefault: true);
        $this->assertSame('Yes', $transformer->transform(TestBasicEnum::Yes));
        $this->assertSame('No', $transformer->transform(TestBackedEnum::No));
        $this->assertFalse($transformer->canTransform('A string', Type::STRING));
    }

    public function testEnumConverterUnsupported(): void
    {
        $transformer = new EnumConverter();
        $this->expectException(TransformerException::class);
        $this->expectExceptionMessage("Enum string for 'string' is not supported.");
        $transformer->transform('A string');
    }

    public function testThrowableConverter(): void
    {
        $subject = new LogicException('Error message', 123, new LogicException('Previous error', 456));
        $transformer = new ThrowableConverter();
        $this->assertEquals((object)[
            'type' => 'LogicException',
            'message' => 'Error message',
            'code' => 123,
        ], $transformer->transform($subject));
        $this->assertSame([
            'type' => 'LogicException',
            'message' => 'Error message',
            'code' => 123,
        ], $transformer->transform($subject, Type::ARRAY));
        $this->assertSame('Error message', $transformer->transform($subject, Type::STRING));
        $this->assertFalse($transformer->canTransform($subject, Type::INTEGER));
        $this->assertFalse($transformer->canTransform('A string', Type::STRING));
    }

    public function testThrowableConverterAllData(): void
    {
        $subject = new LogicException('Error message', 123, new LogicException('Previous error', 456));
        $transformer = new ThrowableConverter(
            parts: ['type', 'message', 'code', 'file', 'line', 'trace', 'previous', 'ignored']
        );
        $result = $transformer->transform($subject);
        $this->assertSame('LogicException', $result->type);
        $this->assertSame('Error message', $result->message);
        $this->assertSame(123, $result->code);
        $this->assertStringEndsWith('TransformerTest.php', $result->file);
        $this->assertIsInt($result->line);
        $this->assertIsArray($result->trace);
        $this->assertInstanceOf(LogicException::class, $result->previous);
    }

    public function testThrowableConverterUnsupported(): void
    {
        $transformer = new ThrowableConverter();
        $this->expectException(TransformerException::class);
        $this->expectExceptionMessage("Throwable conversion for 'string' is not supported.");
        $transformer->transform('A string');
    }

    public function testThrowableConverterInvalidDefault(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid 'integer' provided");
        /* @phpstan-ignore argument.type */
        $transformer = new ThrowableConverter(default: Type::INTEGER);
    }

    public function testFirstMatchResolver(): void
    {
        $subject = new LogicException('Error message', 123);
        $transformer = new FirstMatchResolver([
            new EnumConverter(),
            new ReadableConverter(),
            new ThrowableConverter(),
            new StringableConverter(),
            new BasicTypeConverter(),
        ], Type::STRING);
        $this->assertEquals('Error message', $transformer->transform($subject));
        $this->assertTrue($transformer->canTransform($subject));
        $this->assertSame('Yes', $transformer->transform(TestBasicEnum::Yes));
        $this->assertSame('true', $transformer->transform(true));
        $this->assertSame('Stringable test class', $transformer->transform(new TestStringableObject()));
        $this->assertSame('A string', $transformer->transform('A string'));
    }

    public function testFirstMatchResolverConvertable(): void
    {
        $transformer = new FirstMatchResolver([new StringableConverter()], Type::STRING);
        $this->assertTrue($transformer->canTransform(new TestStringableObject()));
        $this->assertFalse($transformer->canTransform(new TestObject()));
    }

    public function testFirstMatchResolverInvalidTransformer(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("'string' is not implementing Phrity\Util\Transformer\TransformerInterface");
        /* @phpstan-ignore argument.type */
        $transformer = new FirstMatchResolver(['This is highly illegal']);
    }

    public function testFirstMatchResolverNoMatch(): void
    {
        $transformer = new FirstMatchResolver([new EnumConverter()]);
        $this->expectException(TransformerException::class);
        $this->expectExceptionMessage("Could not find transformer for 'string'.");
        $transformer->transform('A string');
    }

    public function testRecursionResolver(): void
    {
        $transformer = new RecursionResolver(new BasicTypeConverter());
        $this->assertSame([
            'a' => [
                'b' => 1,
                'c' => [
                    'd' => 2,
                    'e' => [
                        'public' => 'public',
                    ]
                ]
            ]
        ], $transformer->transform([
            'a' => [
                'b' => 1,
                'c' => [
                    'd' => 2,
                    'e' => new TestObject()
                ]
            ]
        ], Type::ARRAY));
        $this->assertEquals((object)[
            'a' => (object)[
                'b' => 1,
                'c' => (object)[
                    'd' => 2,
                    'e' => (object)[
                        'public' => 'public',
                    ]
                ]
            ]
        ], $transformer->transform((object)[
            'a' => (object)[
                'b' => 1,
                'c' => (object)[
                    'd' => 2,
                    'e' => new TestObject()
                ]
            ]
        ], Type::OBJECT));
        $this->assertTrue($transformer->canTransform('A string'));
        $this->assertSame('A string', $transformer->transform('A string'));
    }
}
