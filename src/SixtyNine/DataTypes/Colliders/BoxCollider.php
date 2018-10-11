<?php

namespace SixtyNine\DataTypes\Colliders;

use SixtyNine\DataTypes\Box;
use SixtyNine\DataTypes\Vector;

/**
 * Class BoxCollider
 * @package SixtyNine\DataTypes\Colliders
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class BoxCollider extends Collider
{
    /** @var Box */
    protected $box;

    public function __construct(Box $box)
    {
        $this->box = $box;
    }

    public function getBoundingBox(): Box
    {
        return $this->box;
    }

    public function collidesWithPoint(Vector $p): bool
    {
        return $p->inside($this->box);
    }
}
