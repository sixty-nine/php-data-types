<?php

namespace SixtyNine\DataTypes;

use JMS\Serializer\Annotation as JMS;

/**
 * An axis-aligned rectangle with collision detection
 */
class Box
{
    /**
     * @var float
     * @JMS\Type("float")
     */
    protected $x;

    /**
     * @var float
     * @JMS\Type("float")
     */
    protected $y;

    /**
     * @var float
     * @JMS\Type("float")
     */
    protected $width;

    /**
     * @var float
     * @JMS\Type("float")
     */
    protected $height;

    /**
     * @var float
     * @JMS\Exclude()
     */
    protected $top;

    /**
     * @var float
     * @JMS\Exclude()
     */
    protected $bottom;

    /**
     * @var float
     * @JMS\Exclude()
     */
    protected $left;

    /**
     * @var float
     * @JMS\Exclude()
     */
    protected $right;

    /**
     * Constructor
     * @param float $x
     * @param float $y
     * @param float $width
     * @param float $height
     */
    public function __construct($x, $y, $width, $height)
    {
        $this->x = $x;
        $this->y = $y;
        $this->width = $width;
        $this->height = $height;

        $this->update();
    }

    /**
     * Factory method.
     * @param float $x
     * @param float $y
     * @param float $width
     * @param float $height
     * @return Box
     */
    public static function create($x, $y, $width, $height)
    {
        return new self($x, $y, $width, $height);
    }

    /**
     * Update the left, right, top, and bottom coordinates.
     */
    public function update()
    {
        $this->left = $this->x;
        $this->right = $this->x + $this->width;
        $this->top = $this->y;
        $this->bottom = $this->y + $this->height;
    }

    /**
     * Detect box collision
     * This algorithm only works with Axis-Aligned boxes!
     * @param Box $box The other rectangle to test collision with
     * @param bool $strict If true, boxes "touching" each other are not intersecting, otherwise they are
     * @return boolean True is the boxes collide, false otherwise
     */
    public function intersects(Box $box, $strict = true)
    {
        $comparator = function ($x, $y) {
            return $x < $y;
        };

        if (!$strict) {
            $comparator = function ($x, $y) {
                return $x <= $y;
            };
        }

        return $comparator($this->getLeft(), $box->getRight())
            && $comparator($box->getLeft(), $this->getRight())
            && $comparator($this->getTop(), $box->getBottom())
            && $comparator($box->getTop(), $this->getBottom())
        ;
    }

    /**
     * @param Box $box
     * @return bool
     */
    public function inside(Box $box)
    {
        return ($this->getLeft() >= $box->getLeft()
            && $this->getRight() <= $box->getRight()
            && $this->getTop() >= $box->getTop()
            && $this->getBottom() <= $box->getBottom()
        );
    }

    /**
     * @param float $deltaX
     * @param float $deltaY
     * @return Box
     */
    public function move($deltaX, $deltaY)
    {
        return new self($this->getX() + $deltaX, $this->getY() + $deltaY, $this->getWidth(), $this->getHeight());
    }

    /**
     * @param int $increment
     * @return Box
     */
    public function resize($increment)
    {
        return new self(
            $this->getX() - $increment,
            $this->getY() - $increment,
            $this->getWidth() + 2 * $increment,
            $this->getHeight() + 2 * $increment
        );
    }

    /**
     * @return float
     */
    public function getBottom()
    {
        return $this->bottom;
    }

    /**
     * @return float
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @return float
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @return float
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * @return float
     */
    public function getTop()
    {
        return $this->top;
    }

    /**
     * @return float
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return float
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * @return float
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * @return Vector
     */
    public function getPosition()
    {
        return new Vector($this->getX(), $this->getY());
    }

    /**
     * @return Vector
     */
    public function getDimensions()
    {
        return new Vector($this->getWidth(), $this->getHeight());
    }

    /**
     * @return Vector
     */
    public function getCenter()
    {
        return new Vector(
            $this->x + $this->width / 2,
            $this->y + $this->height / 2
        );
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('[%s, %s] x [%s, %s]', $this->x, $this->y, $this->width, $this->height);
    }
}

