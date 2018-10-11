<?php

namespace SixtyNine\DataTypes\Colliders;

use SixtyNine\DataTypes\Vector;
use SixtyNine\DataTypes\Box;

/**
 * Class Collider
 * @package SixtyNine\DataTypes\Colliders
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
abstract class Collider implements ColliderInterface
{
    abstract public function getBoundingBox() : Box;

    abstract public function collidesWithPoint(Vector $p) : bool;

    public function collidesWithCollider(ColliderInterface $collider): bool
    {
        $ibb = $this->getBoundingBox()->intersection($collider->getBoundingBox());

        if (!$ibb) {
            return false;
        }

        for ($i = $ibb->getLeft(); $i <= $ibb->getRight(); $i++) {
            for ($j = $ibb->getTop(); $j <= $ibb->getBottom(); $j++) {
                $p = new Vector($i, $j);

                if ($this->collidesWithPoint($p) && $collider->collidesWithPoint($p)) {
                    return true;
                }
            }
        }

        return false;
    }
}
