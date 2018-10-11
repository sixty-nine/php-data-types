<?php

namespace SixtyNine\DataTypes\Colliders;

use SixtyNine\DataTypes\Box;
use SixtyNine\DataTypes\Vector;

/**
 * Interface ColliderInterface
 * @package SixtyNine\DataTypes\Colliders
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
interface ColliderInterface
{
    public function getBoundingBox(): Box;

    public function collidesWithPoint(Vector $p): bool;

    public function collidesWithCollider(ColliderInterface $collider): bool;
}
