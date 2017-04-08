<?php

namespace SixtyNine\DataTypes\Tests;

use SixtyNine\DataTypes\Box;
use SixtyNine\DataTypes\QuadTree;

class QuadTreeTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $bounds = new Box(0, 0, 800, 600);
        $tree = new QuadTree($bounds);
        $this->assertInstanceOf(QuadTree::class, $tree);
        $this->assertAttributeEquals($bounds, 'bounds', $tree);
        $this->assertAttributeEquals(0, 'level', $tree);
        $this->assertAttributeEquals(null, 'nodes', $tree);
        $this->assertEquals(0, $tree->count());
    }

    public function testSplit()
    {
        $bounds = new Box(0, 0, 800, 600);
        $tree = new QuadTree($bounds);
        $tree->split();

        $nodes = $this->readAttribute($tree, 'nodes');
        $this->assertInternalType('array', $nodes);
        $this->assertCount(4, $nodes);

        foreach ($nodes as $node) {
            $this->assertInstanceOf(QuadTree::class, $node);
            $this->assertAttributeEquals(null, 'nodes', $node);
            $nodeBounds = $this->readAttribute($node, 'bounds');
            $this->assertContains($nodeBounds->getX(), array(0, 400));
            $this->assertContains($nodeBounds->getY(), array(0, 300));
            $this->assertEquals(400, $nodeBounds->getWidth());
            $this->assertEquals(300, $nodeBounds->getHeight());
        }
    }

    /**
     * @dataProvider getIndexProvider
     */
    public function testGetIndex($expectedIndex, $box)
    {
        $bounds = new Box(0, 0, 800, 600);
        $tree = new QuadTree($bounds);

        $this->assertEquals($expectedIndex, $tree->getIndex($box));
    }

    /**
     * @return array
     */
    public function getIndexProvider()
    {
        return array(
            array(-1, new Box(0, 0, 800, 600)),
            array(-1, (new Box(0, 0, 400, 300))->resize(1)),
            array(-1, new Box(300, 200, 200, 200)),
            array(0, new Box(0, 0, 400, 300)),
            array(0, (new Box(0, 0, 400, 300))->resize(-1)),
            array(1, new Box(400, 0, 400, 300)),
            array(1, new Box(750, 100, 50, 50)),
            array(2, new Box(0, 300, 400, 300)),
            array(2, new Box(250, 310, 50, 50)),
            array(3, new Box(400, 300, 400, 300)),
            array(3, new Box(700, 500, 50, 50)),
        );
    }

    public function testInsert()
    {
        $bounds = new Box(0, 0, 800, 600);
        $tree = new QuadTree($bounds);

        for ($i = 0; $i < 10; $i++) {
            $tree->insert(new Box(10, $i * 10, 10, 10));
            $this->assertEquals($i + 1, $tree->count());
        }

        $this->assertAttributeEquals(false, 'isSplit', $tree);

        $tree->insert(new Box(10, 110, 10, 10));

        $this->assertAttributeEquals(true, 'isSplit', $tree);
    }

    public function testRetrieve()
    {
        $bounds = new Box(0, 0, 80, 80);
        $tree = new QuadTree($bounds);

        for ($i = 0; $i < 80; $i += 10) {
            for ($j = 0; $j < 80; $j += 10) {
                $tree->insert(new Box($i, $j, 10, 10));
            }
        }

        $nodes = $tree->retrieve(new Box(5, 5, 10, 10));
        $expectedNodes = array(
            new Box(0, 0, 10, 10),
            new Box(10, 0, 10, 10),
            new Box(0, 10, 10, 10),
            new Box(10, 10, 10, 10),
        );
        $this->assertCount(4, $nodes);

        for ($i = 0; $i < 4; $i++) {
            $this->assertTrue(in_array($nodes[$i], $expectedNodes));
        }
    }

    public function testRetrieve1()
    {
        $count = 1000;
        $bounds = new Box(0, 0, 100, 100);
        $tree = new QuadTree($bounds);
        $this->fillTreeWithRandomBoxes($tree, $count);
        $this->assertEquals($count, count($tree->retrieve($bounds)));
        $this->assertEquals($tree->getAllObjects(), $tree->retrieve($bounds));
    }

    public function testCollides()
    {
        $bounds = new Box(0, 0, 80, 80);
        $tree = new QuadTree($bounds);

        for ($i = 0; $i < 80; $i += 10) {
            for ($j = 0; $j < 80; $j += 10) {
                $tree->insert(new Box($i, $j, 10, 10));
            }
        }

        $this->assertTrue($tree->collides(new Box(0, 0, 10, 10)));
        $this->assertTrue($tree->collides(new Box(5, 5, 10, 10)));
        $this->assertTrue($tree->collides(new Box(25, 25, 10, 10)));
        $this->assertTrue($tree->collides(new Box(50, 50, 10, 10)));
        $this->assertTrue($tree->collides(new Box(0, 0, 10, 10)));
        $this->assertTrue($tree->collides(new Box(70, 0, 10, 10)));

        $this->assertFalse($tree->collides(new Box(1000, 1000, 10, 10)));
    }

    public function testCollides1()
    {
        $bounds = new Box(0, 0, 100, 100);
        $tree = new QuadTree($bounds);
        $tree->insert(new Box(25, 25, 50, 50));

        $this->assertTrue($tree->collides(new Box(0, 0, 50, 50)));
        $this->assertTrue($tree->collides(new Box(25, 25, 10, 10)));
        $this->assertTrue($tree->collides(new Box(50, 50, 10, 10)));

        $this->assertFalse($tree->collides(new Box(0, 0, 10, 10)));
        $this->assertFalse($tree->collides(new Box(80, 80, 10, 10)));
        $this->assertFalse($tree->collides(new Box(1000, 1000, 10, 10)));
    }

    public function testCount()
    {
        $bounds = new Box(0, 0, 10000, 10000);
        $tree = new QuadTree($bounds);
        $this->fillTreeWithRandomBoxes($tree, 1000);
        $this->assertEquals(1000, $tree->count());
    }

    public function testGetAllObjects()
    {
        $bounds = new Box(0, 0, 10000, 10000);
        $tree = new QuadTree($bounds);
        $this->fillTreeWithRandomBoxes($tree, 1000);
        $this->assertCount(1000, $tree->getAllObjects());
    }

    /**
     * @param QuadTree $tree
     * @param int $count
     */
    protected function fillTreeWithRandomBoxes(QuadTree $tree, $count)
    {
        $treeWidth = $tree->getBounds()->getWidth();
        $treeHeight = $tree->getBounds()->getHeight();

        for ($i = 0; $i < $count; $i++) {
            $w = rand(0, 10);
            $h = rand(0, 10);
            $box = new Box(rand(0, $treeWidth - $w), rand(0, $treeHeight - $h), $w, $h);
            $tree->insert($box);
        }
    }
}
