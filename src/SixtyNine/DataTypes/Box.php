<?php

namespace SixtyNine\DataTypes;

/**
 * An axis-aligned rectangle with collision detection
 * @package SixtyNine\DataTypes
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class Box
{
    /** @var float */
    protected $x;

    /** @var float */
    protected $y;

    /** @var float */
    protected $width;

    /** @var float */
    protected $height;

    /** @var float */
    protected $top;

    /** @var float */
    protected $bottom;

    /** @var float */
    protected $left;

    /** @var float */
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
    public static function create($x, $y, $width, $height) : Box
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
     * @param Box $box
     * @return null|Box
     */
    public function intersection(Box $box) : ?Box
    {
        $leftX = max($this->getX(), $box->getX());
        $rightX = min($this->getX() + $this->getWidth(), $box->getX() + $box->getWidth());
        $topY = max($this->getY(), $box->getY());
        $bottomY = min($this->getY() + $this->getHeight(), $box->getY() + $box->getHeight());

        if ($leftX >= $rightX || $topY >= $bottomY) {
            return null;
        }

        return new Box($leftX, $topY, $rightX - $leftX, $bottomY - $topY);
    }

    /**
     * Detect box collision
     * This algorithm only works with Axis-Aligned boxes!
     * @param Box $box The other rectangle to test collision with
     * @param bool $strict If true, boxes "touching" each other are not intersecting, otherwise they are
     * @return boolean True is the boxes collide, false otherwise
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function intersects(Box $box, $strict = true) : bool
    {
        $class = Comparators::class;
        $comparator = $strict ? array($class, 'strictComparator') : array($class, 'nonStrictComparator');

        return $comparator($this->getLeft(), $box->getRight())
            && $comparator($box->getLeft(), $this->getRight())
            && $comparator($this->getTop(), $box->getBottom())
            && $comparator($box->getTop(), $this->getBottom());
    }

    /**
     * @param Box $box
     * @param bool $strict
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function inside(Box $box, $strict = false) : bool
    {
        $class = Comparators::class;
        $comparator = $strict ? array($class, 'strictComparator') : array($class, 'nonStrictComparator');

        return $comparator($box->getLeft(), $this->getLeft())
            && $comparator($this->getRight(), $box->getRight())
            && $comparator($box->getTop(), $this->getTop())
            && $comparator($this->getBottom(), $box->getBottom());
    }

    /**
     * @param float $deltaX
     * @param float $deltaY
     * @return Box
     */
    public function move($deltaX, $deltaY) : Box
    {
        return new self($this->getX() + $deltaX, $this->getY() + $deltaY, $this->getWidth(), $this->getHeight());
    }

    /**
     * @param int $increment
     * @return Box
     */
    public function resize($increment) : Box
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
    public function getBottom() : float
    {
        return $this->bottom;
    }

    /**
     * @return float
     */
    public function getHeight() : float
    {
        return $this->height;
    }

    /**
     * @return float
     */
    public function getLeft() : float
    {
        return $this->left;
    }

    /**
     * @return float
     */
    public function getRight() : float
    {
        return $this->right;
    }

    /**
     * @return float
     */
    public function getTop() : float
    {
        return $this->top;
    }

    /**
     * @return float
     */
    public function getWidth() : float
    {
        return $this->width;
    }

    /**
     * @return float
     */
    public function getX() : float
    {
        return $this->x;
    }

    /**
     * @return float
     */
    public function getY() : float
    {
        return $this->y;
    }

    /**
     * @return Vector
     */
    public function getPosition() : Vector
    {
        return new Vector($this->getX(), $this->getY());
    }

    /**
     * @return Vector
     */
    public function getDimensions() : Vector
    {
        return new Vector($this->getWidth(), $this->getHeight());
    }

    /**
     * @return Vector
     */
    public function getCenter() : Vector
    {
        return new Vector(
            $this->x + $this->width / 2,
            $this->y + $this->height / 2
        );
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        return sprintf('[%s, %s] x [%s, %s]', $this->x, $this->y, $this->width, $this->height);
    }

    public function serialize(): string
    {
        return json_encode([
            'x' => $this->x,
            'y' => $this->y,
            'width' => $this->width,
            'height' => $this->height,
        ]);
    }
}
