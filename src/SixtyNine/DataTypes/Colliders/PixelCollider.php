<?php

namespace SixtyNine\DataTypes\Colliders;

use SixtyNine\DataTypes\Box;
use SixtyNine\DataTypes\Bitmap;
use SixtyNine\DataTypes\Vector;

/**
 * Class PixelCollider
 * @package SixtyNine\DataTypes\Colliders
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class PixelCollider extends Collider
{
    /** @var Bitmap */
    protected $mask;
    /** @var Vector */
    protected $pos;

    public function __construct(Vector $pos, Bitmap $mask)
    {
        $this->pos = $pos;
        $this->mask = $mask;
    }

    public function getBoundingBox(): Box
    {
        return new Box(
            $this->pos->getX(),
            $this->pos->getY(),
            $this->mask->getWidth(),
            $this->mask->getHeight()
        );
    }

    public function collidesWithPoint(Vector $p): bool
    {
        return (bool)$this->mask->get(
            $p->getX() - $this->pos->getX(),
            $p->getY() - $this->pos->getY()
        );
    }
}
