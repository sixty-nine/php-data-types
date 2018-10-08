<?php

namespace SixtyNine\DataTypes;

/**
 * Class Vector
 * @package SixtyNine\DataTypes
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class Vector
{
    /**
     * @var float
     */
    protected $x;

    /**
     * @var float
     */
    protected $y;

    /**
     * Constructor
     * @param float $x
     * @param float $y
     */
    public function __construct($x = 0.0, $y = 0.0)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * Factory method.
     * @param float $x
     * @param float $y
     * @return Vector
     */
    public static function create($x = 0.0, $y = 0.0) : Vector
    {
        return new self($x, $y);
    }

    /**
     * Get Y coordinate.
     * @param float $x
     * @return Vector
     */
    public function setX($x) : Vector
    {
        $this->x = $x;
        return $this;
    }

    /**
     * Get X coordinate.
     * @return float
     */
    public function getX() : float
    {
        return $this->x;
    }

    /**
     * Set Y coordinate.
     * @param float $y
     * @return Vector
     */
    public function setY($y) : Vector
    {
        $this->y = $y;
        return $this;
    }

    /**
     * Get Y coordinate.
     * @return float
     */
    public function getY() : float
    {
        return $this->y;
    }

    /**
     * Length of vector (magnitude)
     * @return float
     */
    public function length() : float
    {
        return sqrt($this->x * $this->x + $this->y * $this->y);
    }

    /**
     * Scalar displacement.
     * @param float $deltaX
     * @param float $deltaY
     * @return Vector
     */
    public function move($deltaX, $deltaY) : Vector
    {
        return new self($this->x + $deltaX, $this->y + $deltaY);
    }

    /**
     * Scalar multiplication.
     * @param float $factor
     * @return Vector
     */
    public function mult($factor) : Vector
    {
        return new self($this->x * $factor, $this->y * $factor);
    }

    /**
     * Vector addition.
     * @param Vector $other
     * @return Vector
     */
    public function add(Vector $other) : Vector
    {
        return new self($this->x + $other->getX(), $this->y + $other->getY());
    }

    /**
     * Dot product.
     * @param Vector $other
     * @return float
     */
    public function dot(Vector $other) : float
    {
        return $this->x * $other->getX() + $this->y * $other->getY();
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

        return $comparator($box->getLeft(), $this->x)
            && $comparator($this->x, $box->getRight())
            && $comparator($box->getTop(), $this->y)
            && $comparator($this->y, $box->getBottom());
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        return sprintf('[%s, %s]', $this->x, $this->y);
    }

    public function serialize() : string
    {
        return json_encode([
            'x' => $this->x,
            'y' => $this->y,
        ]);
    }
}
