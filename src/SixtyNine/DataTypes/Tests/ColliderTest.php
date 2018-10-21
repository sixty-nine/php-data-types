<?php

namespace SixtyNine\DataTypes\Tests;

use PHPUnit\Framework\TestCase;
use SixtyNine\DataTypes\Box;
use SixtyNine\DataTypes\Colliders\BoxCollider;
use SixtyNine\DataTypes\Colliders\CircleCollider;
use SixtyNine\DataTypes\Colliders\CompoundCollider;
use SixtyNine\DataTypes\Colliders\PixelCollider;
use SixtyNine\DataTypes\Bitmap;
use SixtyNine\DataTypes\Vector;

class ColliderTest extends TestCase
{
    public function testCircleCollider()
    {
        $c1 = new CircleCollider(Vector::create(0, 0), 5);
        $c2 = new CircleCollider(Vector::create(10, 0), 5);
        $c3 = new CircleCollider(Vector::create(5, 0), 2);
        $c4 = new CircleCollider(Vector::create(0, 0), 10);

        $this->assertFalse($c1->collidesWithColLider($c2));
        $this->assertFalse($c2->collidesWithColLider($c1));
        $this->assertTrue($c1->collidesWithColLider($c3));
        $this->assertTrue($c3->collidesWithColLider($c1));
        $this->assertTrue($c2->collidesWithColLider($c3));
        $this->assertTrue($c3->collidesWithColLider($c2));

        $this->assertTrue($c1->collidesWithColLider($c4));
        $this->assertTrue($c2->collidesWithColLider($c4));
        $this->assertTrue($c3->collidesWithColLider($c4));
        $this->assertTrue($c4->collidesWithColLider($c4));

        $this->assertTrue($c4->collidesWithColLider($c1));
        $this->assertTrue($c4->collidesWithColLider($c2));
        $this->assertTrue($c4->collidesWithColLider($c3));
        $this->assertTrue($c4->collidesWithColLider($c4));
    }

    public function testBoxCollider()
    {
        $b1 = new BoxCollider(Box::create(0, 0, 5, 5));
        $b2 = new BoxCollider(Box::create(4, 4, 7, 7));
        $b3 = new BoxCollider(Box::create(10, 10, 5, 5));

        $this->assertTrue($b1->collidesWithColLider($b2));
        $this->assertTrue($b2->collidesWithColLider($b1));
        $this->assertTrue($b2->collidesWithColLider($b3));
        $this->assertTrue($b3->collidesWithColLider($b2));
        $this->assertFalse($b1->collidesWithColLider($b3));
        $this->assertFalse($b3->collidesWithColLider($b1));
    }

    public function testMixedColliders()
    {
        $c1 = new CircleCollider(Vector::create(0, 0), 6);
        $c2 = new CircleCollider(Vector::create(10, 10), 6);
        $b1 = new BoxCollider(Box::create(4, 4, 7, 7));

        $this->assertTrue($c1->collidesWithColLider($b1));
        $this->assertTrue($b1->collidesWithColLider($c1));
        $this->assertTrue($c2->collidesWithColLider($b1));
        $this->assertTrue($b1->collidesWithColLider($c2));
        $this->assertFalse($c1->collidesWithColLider($c2));
    }

    public function testPixelCollider()
    {
        $mask = new Bitmap(4, 4, [[0, 0, 0, 0], [0, 0, 0, 0], [0, 0, 1, 1], [0, 0, 1, 1]]);
        $p = new PixelCollider(Vector::create(0, 0), $mask);
        $b1 = new BoxCollider(Box::create(-1, -1, 2, 2));
        $b2 = new BoxCollider(Box::create(2, 2, 2, 2));

        $this->assertFalse($p->collidesWithColLider($b1));
        $this->assertTrue($p->collidesWithColLider($b2));
    }

    public function testCompoundCollider()
    {
        $b1 = new BoxCollider(Box::create(0, 0, 1, 1));
        $b2 = new BoxCollider(Box::create(2, 2, 1, 1));
        $b3 = new BoxCollider(Box::create(2, 0, 1, 1));
        $b4 = new BoxCollider(Box::create(0, 2, 1, 1));
        $c1 = new CompoundCollider([$b1, $b2]);
        $c2 = new CompoundCollider([$b3, $b4]);

        $this->assertFalse($b1->collidesWithCollider($b3));
        $this->assertFalse($b1->collidesWithCollider($b4));
        $this->assertFalse($b2->collidesWithCollider($b3));
        $this->assertFalse($b2->collidesWithCollider($b4));
        $this->assertFalse($c1->collidesWithCollider($c2));
    }
}
