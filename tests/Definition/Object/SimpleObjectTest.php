<?php

/*
 * This file is part of the Alice package.
 *
 * (c) Nelmio <hello@nelm.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Nelmio\Alice\Definition\Object;

use Nelmio\Alice\Entity\StdClassFactory;
use Nelmio\Alice\ObjectInterface;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;
use stdClass;

/**
 * @covers \Nelmio\Alice\Definition\Object\SimpleObject
 */
class SimpleObjectTest extends TestCase
{
    /**
     * @var ReflectionProperty
     */
    private $propRefl;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->propRefl = (new ReflectionClass(SimpleObject::class))->getProperty('instance');
        $this->propRefl->setAccessible(true);
    }

    public function testIsAnObject(): void
    {
        static::assertTrue(is_a(SimpleObject::class, ObjectInterface::class, true));
    }

    public function testReadAccessorsReturnPropertiesValues(): void
    {
        $reference = 'user0';
        $instance = new stdClass();

        $object = new SimpleObject($reference, $instance);

        static::assertEquals($reference, $object->getId());
        static::assertEquals($instance, $object->getInstance());
    }

    public function testIsNotImmutable(): void
    {
        $reference = 'user0';
        $instance = new stdClass();

        $object = new SimpleObject($reference, $instance);

        // Mutate injected values
        $instance->foo = 'bar';

        // Mutate returned values
        $object->getInstance()->ping = 'pong';

        $expected = StdClassFactory::create(['foo' => 'bar', 'ping' => 'pong']);
        $actual = $object->getInstance();

        static::assertEquals($expected, $actual);
    }

    public function testNamedConstructor(): void
    {
        $reference = 'user0';
        $instance = StdClassFactory::create(['original' => true]);
        $originalInstance = clone $instance;
        $object = new SimpleObject($reference, $instance);

        $newInstance = StdClassFactory::create(['original' => false]);
        $originalNewInstance = clone $newInstance;
        $newObject = $object->withInstance($newInstance);

        static::assertEquals(new SimpleObject($reference, $originalInstance), $object);
        static::assertEquals(new SimpleObject($reference, $originalNewInstance), $newObject);
    }
}
