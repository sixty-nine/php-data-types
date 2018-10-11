<?php

namespace SixtyNine\DataTypes\Mask;

use SixtyNine\DataTypes\Box;

interface MaskInterface
{
    /**
     * @param Box $box
     * @return BoxList
     */
    public function insert(Box $box);

    /** @return int */
    public function count();

    /** @return array */
    public function all();

    /**
     * @param Box $box
     * @return array
     */
    public function getIntersecting(Box $box);

    /**
     * @param Box $box
     * @return bool
     */
    public function collides(Box $box);
}
