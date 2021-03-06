<?php

namespace SixtyNine\DataTypes;

/**
 * Class Comparators
 * @package SixtyNine\DataTypes
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class Comparators
{
    /**
     * @param float $x
     * @param float $y
     * @return bool
     */
    public static function strictComparator($x, $y)
    {
        return $x < $y;
    }

    /**
     * @param float $x
     * @param float $y
     * @return bool
     */
    public static function nonStrictComparator($x, $y)
    {
        return $x <= $y;
    }
}
