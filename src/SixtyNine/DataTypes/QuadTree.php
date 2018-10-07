<?php

namespace SixtyNine\DataTypes;

use Doctrine\Common\Collections\ArrayCollection;

class QuadTree
{
    const Q_TOP_LEFT = 0;
    const Q_TOP_RIGHT = 1;
    const Q_BOTTOM_LEFT = 2;
    const Q_BOTTOM_RIGHT = 3;

    /** @var int */
    protected $level;
    /** @var int */
    protected $maxObjects;
    /** @var int */
    protected $maxLevels;
    /** @var ArrayCollection */
    protected $objects;
    /** @var \SixtyNine\DataTypes\Box */
    protected $bounds;
    /** @var bool */
    protected $isSplit = false;
    /** @var array QuadTree[] */
    protected $nodes;

    /**
     * @param Box $bounds
     * @param int $level
     * @param int $maxObjects
     * @param int $maxLevels
     */
    public function __construct(Box $bounds, $level = 0, $maxObjects = 10, $maxLevels = 10)
    {
        $this->level = $level;
        $this->maxObjects = $maxObjects;
        $this->maxLevels = $maxLevels;
        $this->objects = new ArrayCollection();
        $this->bounds = $bounds;
    }

    public function split()
    {
        $this->isSplit = true;

        $subWidth = (int)($this->bounds->getWidth() / 2);
        $subHeight = (int)($this->bounds->getHeight() / 2);

        $x = $this->bounds->getX();
        $y = $this->bounds->getY();

        $this->nodes = array();
        $this->nodes[self::Q_TOP_LEFT] = new Quadtree(new Box($x, $y, $subWidth, $subHeight), $this->level + 1, $this->maxObjects, $this->maxLevels);
        $this->nodes[self::Q_TOP_RIGHT] = new Quadtree(new Box($x + $subWidth, $y, $subWidth, $subHeight), $this->level + 1, $this->maxObjects, $this->maxLevels);
        $this->nodes[self::Q_BOTTOM_LEFT] = new Quadtree(new Box($x, $y + $subHeight, $subWidth, $subHeight), $this->level + 1, $this->maxObjects, $this->maxLevels);
        $this->nodes[self::Q_BOTTOM_RIGHT] = new Quadtree(new Box($x + $subWidth, $y + $subHeight, $subWidth, $subHeight), $this->level + 1, $this->maxObjects, $this->maxLevels);
    }

    /**
     * @param Box $box
     * @return bool
     */
    protected function isInTopQuadrant(Box $box)
    {
        $hMidpoint = $this->bounds->getCenter()->getY();
        return $box->getY() <= $hMidpoint && $box->getY() + $box->getHeight() <= $hMidpoint;
    }

    /**
     * @param Box $box
     * @return bool
     */
    protected function isInBottomQuadrant(Box $box)
    {
        $hMidpoint = $this->bounds->getCenter()->getY();
        return $box->getY() >= $hMidpoint;
    }

    /**
     * @param Box $box
     * @return bool
     */
    protected function isInLeftQuadrant(Box $box)
    {
        $vMidpoint = $this->bounds->getCenter()->getX();
        return $box->getX() <= $vMidpoint && $box->getX() + $box->getWidth() <= $vMidpoint;
    }

    /**
     * @param Box $box
     * @return bool
     */
    protected function isInRightQuadrant(Box $box)
    {
        $vMidpoint = $this->bounds->getCenter()->getX();
        return $box->getX() >= $vMidpoint;
    }

    /**
     * @param Box $box
     * @return int
     */
    public function getIndex(Box $box)
    {
        $topQuadrant = $this->isInTopQuadrant($box);
        $bottomQuadrant = $this->isInBottomQuadrant($box);
        $leftQuadrant = $this->isInLeftQuadrant($box);
        $rightQuadrant = $this->isInRightQuadrant($box);

        switch (true) {
            case $leftQuadrant && $topQuadrant:
                return self::Q_TOP_LEFT;
            case $rightQuadrant && $topQuadrant:
                return self::Q_TOP_RIGHT;
            case $leftQuadrant && $bottomQuadrant:
                return self::Q_BOTTOM_LEFT;
            case $rightQuadrant && $bottomQuadrant:
                return self::Q_BOTTOM_RIGHT;
        }

        return -1;
    }

    /**
     * @param Box $box
     */
    public function insert(Box $box)
    {
        if ($this->isSplit) {
            $index = $this->getIndex($box);
            if ($index !== -1) {
                /** @var QuadTree $node */
                $node = $this->nodes[$index];
                $node->insert($box);
                return;
            }
        }

        $this->objects->add($box);

        if (count($this->objects) > $this->maxObjects && $this->level < $this->maxLevels) {
            if (!$this->isSplit) {
                $this->split();
            }

            foreach ($this->objects as $object) {
                $index = $this->getIndex($object);
                if ($index !== -1) {
                    $this->objects->removeElement($object);
                    /** @var QuadTree $node */
                    $node = $this->nodes[$index];
                    $node->insert($object);
                }
            }
        }
    }

    /**
     * @return int
     */
    public function count()
    {
        $count = $this->objects->count();

        if ($this->isSplit) {
            /** @var QuadTree $node */
            foreach ($this->nodes as $node) {
                $count += $node->count();
            }
        }

        return $count;
    }

    /**
     * @param Box $box
     * @return array
     */
    public function retrieve(Box $box)
    {
        $return = array();

        if (!$this->bounds->intersects($box, false)) {
            return array();
        }

        /** @var Box $object */
        foreach ($this->objects as $object) {
            if ($object->intersects($box, false)) {
                $return[] = $object;
            }
        }

        if ($this->isSplit) {
            /** @var QuadTree $node */
            foreach ($this->nodes as $node) {
                $return = array_merge($return, $node->retrieve($box));
            }
        }

        return $return;
    }

    /**
     * @param Box $box
     * @return bool
     */
    public function collides(Box $box)
    {
        foreach ($this->objects as $object) {
            if ($box->intersects($object)) {
                return true;
            }
        }

        if ($this->isSplit) {

            $index = $this->getIndex($box);
            $nodes = (-1 === $index) ? $this->nodes : array($this->nodes[$index]);
            /** @var QuadTree $node */
            foreach ($nodes as $node) {
                if ($node->collides($box)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function __toString()
    {
        $padding = str_repeat('> ', $this->level);
        $res = sprintf(
            '%sBounds: %s, objects: %s',
            $padding,
            $this->bounds,
            $this->objects->count()
        );

        foreach ($this->objects as $box) {
            $res .= PHP_EOL . $padding . '  - ' . (string)$box;
        }

        if (null !== $this->nodes) {
            foreach ($this->nodes as $node) {
                $res .= PHP_EOL . (string)$node;
            }
        }

        return $res;
    }

    /**
     * @return array
     */
    public function getAllObjects()
    {
        $return = array();

        $return = array_merge($return, $this->objects->toArray());

        if ($this->isSplit) {
            /** @var QuadTree $node */
            foreach ($this->nodes as $node) {
                $return = array_merge($return, $node->getAllObjects());
            }
        }

        return $return;
    }

    /**
     * @return Box
     */
    public function getBounds()
    {
        return $this->bounds;
    }
}
