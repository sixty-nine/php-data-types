<?php

namespace SixtyNine\DataTypes\Mask;

use SixtyNine\DataTypes\Box;
use SixtyNine\DataTypes\Colliders\ColliderInterface;

/**
 * Class BoxList
 * @package SixtyNine\DataTypes\Mask
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class ListMask implements MaskInterface
{
    protected $list = [];

    /**
     * @param ColliderInterface $collider
     * @return MaskInterface
     */
    public function insert(ColliderInterface $collider) : MaskInterface
    {
        $uid = md5($collider->getBoundingBox()->serialize());
        if (!array_key_exists($uid, $this->list)) {
            $this->list[$uid] = $collider;
        }
        return $this;
    }

    /** @return int */
    public function count() : int
    {
        return count($this->list);
    }

    /** @return array */
    public function all() : array
    {
        return array_values($this->list);
    }

    /**
     * @param ColliderInterface $collider
     * @return array
     */
    public function getCollisions(ColliderInterface $collider) : array
    {
        $list = [];
        foreach ($this->list as $other) {
            if ($collider->getBoundingBox()->intersects($other->getBoundingBox(), false)) {
                $list[] = $other;
            }
        }
        return $list;
    }

    /**
     * @param ColliderInterface $collider
     * @return bool
     */
    public function collides(ColliderInterface $collider) : bool
    {
        foreach ($this->list as $other) {
            if ($collider->getBoundingBox()->intersects($other->getBoundingBox(), false)) {
                return true;
            }
        }
        return false;
    }

    /** @return Box */
    public function getBounds(): Box
    {
        $minX = $minY = PHP_INT_MAX;
        $maxX = $maxY = -PHP_INT_MAX;

        /** @var Box $box */
        foreach ($this->all() as $box) {
            $minX = min($box->getLeft(), $minX);
            $minY = min($box->getTop(), $minY);
            $maxX = max($box->getRight(), $maxX);
            $maxY = max($box->getBottom(), $maxY);
        }

        return new Box($minX, $minY, $maxX - $minX, $maxY - $minY);
    }
}
