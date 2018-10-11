<?php

namespace SixtyNine\DataTypes\Colliders;

use SixtyNine\DataTypes\Box;
use SixtyNine\DataTypes\Vector;

/**
 * Class CircleCollider
 * @package SixtyNine\DataTypes\Colliders
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class CircleCollider extends Collider
{
    /** @var Vector */
    protected $center;
    /** @var float */
    protected $radius;

    public function __construct(Vector $center, float $radius)
    {
        $this->center = $center;
        $this->radius = $radius;
    }

    public function getBoundingBox(): Box
    {
        return new Box(
            $this->center->getX() - $this->radius,
            $this->center->getY() - $this->radius,
            2 * $this->radius,
            2 * $this->radius
        );
    }

    public function collidesWithPoint(Vector $p): bool
    {
        return $this->center->mult(-1)->add($p)->length() <= $this->radius;
    }
}
