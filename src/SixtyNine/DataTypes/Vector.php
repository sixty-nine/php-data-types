<?php

namespace SixtyNine\DataTypes;

use JMS\Serializer\Annotation as JMS;

class Vector
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
    public static function create($x = 0.0, $y = 0.0)
    {
        return new self($x, $y);
    }

    /**
     * Get Y coordinate.
     * @param float $x
     * @return Vector
     */
    public function setX($x)
    {
        $this->x = $x;
        return $this;
    }

    /**
     * Get X coordinate.
     * @return float
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * Set Y coordinate.
     * @param float $y
     * @return Vector
     */
    public function setY($y)
    {
        $this->y = $y;
        return $this;
    }

    /**
     * Get Y coordinate.
     * @return float
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * Length of vector (magnitude)
     * @return float
     */
    public function length()
    {
        return sqrt($this->x * $this->x + $this->y * $this->y);
    }

    /**
     * Scalar displacement.
     * @param float $deltaX
     * @param float $deltaY
     * @return Vector
     */
    public function move($deltaX, $deltaY)
    {
        return new self($this->x + $deltaX, $this->y + $deltaY);
    }

    /**
     * Scalar multiplication.
     * @param float $factor
     * @return Vector
     */
    public function mult($factor)
    {
        return new self($this->x * $factor, $this->y * $factor);
    }

    /**
     * Vector addition.
     * @param Vector $other
     * @return Vector
     */
    public function add(Vector $other)
    {
        return new self($this->x + $other->getX(), $this->y + $other->getY());
    }

    /**
     * Dot product.
     * @param Vector $other
     * @return float
     */
    public function dot(Vector $other)
    {
        return $this->x * $other->getX() + $this->y * $other->getY();
    }

    /**
     * @param Box $box
     * @return bool
     */
    public function inside(Box $box)
    {
        return ($this->x >= $box->getLeft()
            && $this->x <= $box->getRight()
            && $this->y >= $box->getTop()
            && $this->y <= $box->getBottom()
        );
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('[%s, %s]', $this->x, $this->y);
    }
}
