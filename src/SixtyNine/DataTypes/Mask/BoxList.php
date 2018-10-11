<?php

namespace SixtyNine\DataTypes\Mask;

use SixtyNine\DataTypes\Box;

/**
 * Class BoxList
 * @package SixtyNine\DataTypes\Mask
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class BoxList implements MaskInterface
{
    protected $list = [];

    /**
     * @param Box $box
     * @return BoxList
     */
    public function insert(Box $box)
    {
        $uid = md5($box->serialize());
        if (!array_key_exists($uid, $this->list)) {
            $this->list[$uid] = $box;
        }
        return $this;
    }

    /** @return int */
    public function count()
    {
        return count($this->list);
    }

    /** @return array */
    public function all()
    {
        return array_values($this->list);
    }

    /**
     * @param Box $box
     * @return array
     */
    public function getIntersecting(Box $box)
    {
        $list = [];
        foreach ($this->list as $other) {
            if ($box->intersects($other, false)) {
                $list[] = $other;
            }
        }
        return $list;
    }

    /**
     * @param Box $box
     * @return bool
     */
    public function collides(Box $box)
    {
        foreach ($this->list as $other) {
            if ($box->intersects($other, false)) {
                return true;
            }
        }
        return false;
    }
}
