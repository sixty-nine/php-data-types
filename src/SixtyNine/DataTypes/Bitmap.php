<?php

namespace SixtyNine\DataTypes;

use Webmozart\Assert\Assert;

/**
 * Class PixelMask
 * @package SixtyNine\DataTypes
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class Bitmap
{
    /** @var array */
    protected $pixels = [];
    /** @var int */
    protected $width;
    /** @var int */
    protected $height;

    public function __construct(int $width, int $height, array $mask = [])
    {
        $this->width = $width;
        $this->height = $height;

        for ($i = 0; $i <= $width; $i++) {
            for ($j = 0; $j <= $height; $j++) {
                $this->pixels[$i][$j] = isset($mask[$i][$j]) ? $mask[$i][$j] : 0;
            }
        }
    }

    /** @return int */
    public function getWidth(): int
    {
        return $this->width;
    }

    /** @return int */
    public function getHeight(): int
    {
        return $this->height;
    }

    public function set(int $x, int $y, $value)
    {
        $this->assertInBound($x, $y);
        $this->pixels[$x][$y] = $value;
    }

    public function get(int $x, int $y)
    {
        $this->assertInBound($x, $y);
        return $this->pixels[$x][$y];
    }

    protected function assertInBound($x, $y)
    {
        Assert::true(
            $x >= 0 && $x <= $this->width && $y >= 0 && $y <= $this->height,
            'Index out of bounds'
        );
    }
}
