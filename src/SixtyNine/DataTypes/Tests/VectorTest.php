<?php

namespace SixtyNine\DataTypes\Tests;

use JMS\Serializer\SerializerBuilder;
use SixtyNine\DataTypes\Box;
use SixtyNine\DataTypes\Vector;

class VectorTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $v = new Vector(1, 2);
        $this->assertInstanceOf(Vector::class, $v);
        $this->assertEquals(1, $v->getX());
        $this->assertEquals(2, $v->getY());
    }

    public function testConstructorNoParams()
    {
        $v = new Vector();
        $this->assertInstanceOf(Vector::class, $v);
        $this->assertEquals(0, $v->getX());
        $this->assertEquals(0, $v->getY());
    }

    public function testCreate()
    {
        $v = Vector::create(1, 2);
        $this->assertEquals(new Vector(1, 2), $v);
    }

    public function testCreateNoParams()
    {
        $v = Vector::create();
        $this->assertEquals(new Vector(), $v);
    }

    public function testSetters()
    {
        $v = new Vector();
        $v->setX(66)->setY(99);
        $this->assertEquals(66, $v->getX());
        $this->assertEquals(99, $v->getY());
    }

    public function testLength()
    {
        $this->assertEquals(sqrt(2), Vector::create(1, 1)->length());
        $this->assertEquals(2, Vector::create(2, 0)->length());
        $this->assertEquals(2, Vector::create(0, 2)->length());
    }

    public function testMove()
    {
        $v = Vector::create(1, 2)->move(10, 20);
        $this->assertEquals(11, $v->getX());
        $this->assertEquals(22, $v->getY());
    }

    public function testMult()
    {
        $v = Vector::create(1, 2)->mult(10);
        $this->assertEquals(10, $v->getX());
        $this->assertEquals(20, $v->getY());
    }

    public function testAdd()
    {
        $v1 = Vector::create(1, 2);
        $v2 = Vector::create(3, 4);
        $v3 = $v1->add($v2);
        $v4 = $v2->add($v1);
        $this->assertEquals(4, $v3->getX());
        $this->assertEquals(6, $v3->getY());
        $this->assertEquals($v3, $v4);
    }

    public function testDot()
    {
        $v1 = Vector::create(5, 5);
        $v2 = Vector::create(0, 4);
        $s = $v1->dot($v2);
        $this->assertEquals($v1->length() * $v2->length() * cos(pi() / 4), $s);
    }

    public function testToString()
    {
        $this->assertEquals('[123, 321]', (string)Vector::create(123, 321));
    }

    public function testSerialize()
    {
        $data = SerializerBuilder::create()
            ->build()
            ->serialize(Vector::create(123, 321), 'json')
        ;
        $this->assertEquals('{"x":123,"y":321}', $data);
    }

    /**
     * @dataProvider insideProvider
     */
    public function testInside(Vector $v, Box $b, $shouldBeInside)
    {
        $this->assertTrue($shouldBeInside === $v->inside($b));
    }

    /**
     * @return array
     */
    public function insideProvider()
    {
        return array(
            array(Vector::create(0, 0), Box::create(0, 0, 10, 10), true),
            array(Vector::create(5, 5), Box::create(0, 0, 10, 10), true),
            array(Vector::create(10, 10), Box::create(0, 0, 10, 10), true),
            array(Vector::create(15, 15), Box::create(0, 0, 10, 10), false),
        );
    }
}
