<?php

namespace SixtyNine\DataTypes\Tests;

use JMS\Serializer\SerializerBuilder;
use SixtyNine\DataTypes\Box;
use SixtyNine\DataTypes\Vector;

class BoxTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $box = new Box(1, 2, 3, 4);
        $this->assertInstanceOf(Box::class, $box);
        $this->assertEquals(1, $box->getX());
        $this->assertEquals(2, $box->getY());
        $this->assertEquals(3, $box->getWidth());
        $this->assertEquals(4, $box->getHeight());
        $this->assertEquals(1, $box->getLeft());
        $this->assertEquals(4, $box->getRight());
        $this->assertEquals(2, $box->getTop());
        $this->assertEquals(6, $box->getBottom());
        $this->assertEquals(new Vector(1, 2), $box->getPosition());
        $this->assertEquals(new Vector(3, 4), $box->getDimensions());
        $this->assertEquals(new Vector(2.5, 4), $box->getCenter());
    }

    public function testCreate()
    {
        $box = Box::create(1, 2, 3, 4);
        $this->assertEquals(new Box(1, 2, 3, 4), $box);
    }

    public function testToString()
    {
        $box = Box::create(11, 22, 33, 44);
        $this->assertEquals('[11, 22] x [33, 44]', (string)$box);
    }

    public function testMove()
    {
        $box = Box::create(1, 2, 3, 4)->move(10, 20);
        $this->assertEquals(Box::create(11, 22, 3, 4), $box);
    }

    public function testResize()
    {
        $box = Box::create(1, 2, 3, 4)->resize(1);
        $this->assertEquals(Box::create(0, 1, 5, 6), $box);

        $box = Box::create(1, 2, 3, 4)->resize(-1);
        $this->assertEquals(Box::create(2, 3, 1, 2), $box);
    }

    /**
     * @dataProvider intersectBoxesProvider
     */
    public function testIntersect(Box $b1, Box $b2, $shouldCollide)
    {
        $collide = $b1->intersects($b2);

        if ($collide && !$shouldCollide) {
            $this->fail('Collision not expected');
        }

        if (!$collide && $shouldCollide) {
            $this->fail('Collision expected');
        }
    }

    /**
     * @return array
     */
    public function intersectBoxesProvider()
    {
        return array(
            array(new Box(1, 1, 1, 1), new Box(2, 2, 1, 1), false),
            array(new Box(1, 1, 1, 1), new Box(1, 1, 1, 1), true),
            array(new Box(10, 10, 100, 50), new Box(5, 5, 5, 50), false),
            array(new Box(10, 10, 100, 50), new Box(5, 5, 50, 50), true),
            array(new Box(0, 10, 100, 10), new Box(10, 0, 10, 100), true),
        );
    }

    /**
     * @dataProvider insideBoxesProvider
     */
    public function testInside(Box $b1, Box $b2, $shouldBeInside)
    {
        $this->assertTrue($shouldBeInside === $b2->inside($b1));
    }

    /**
     * @return array
     */
    public function insideBoxesProvider()
    {
        return array(
            array(Box::create(10, 10, 10, 10), Box::create(15, 15, 2, 2), true),
            array(Box::create(15, 15, 2, 2), Box::create(10, 10, 10, 10), false),
        );
    }

    public function testSerialize()
    {
        $data = SerializerBuilder::create()
            ->build()
            ->serialize(Box::create(123, 321, 111, 222), 'json')
        ;
        $this->assertEquals('{"x":123,"y":321,"width":111,"height":222}', $data);
    }
}
