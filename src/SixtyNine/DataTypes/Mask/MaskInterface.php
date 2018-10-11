<?php

namespace SixtyNine\DataTypes\Mask;

use SixtyNine\DataTypes\Box;
use SixtyNine\DataTypes\Colliders\ColliderInterface;

interface MaskInterface
{
    /**
     * @param Box $box
     * @return MaskInterface
     */
    public function insert(ColliderInterface $collider) : MaskInterface;

    /** @return int */
    public function count() : int;

    /** @return array */
    public function all() : array;

    /** @return Box */
    public function getBounds() : Box;

    /**
     * @param Box $box
     * @return array
     */
    public function getCollisions(ColliderInterface $collider) : array;

    /**
     * @param Box $box
     * @return bool
     */
    public function collides(ColliderInterface $collider) : bool;
}
