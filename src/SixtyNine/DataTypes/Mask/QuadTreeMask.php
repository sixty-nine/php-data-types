<?php

namespace SixtyNine\DataTypes\Mask;

use SixtyNine\DataTypes\Box;
use SixtyNine\DataTypes\Colliders\ColliderInterface;
use SixtyNine\DataTypes\QuadTree;

class QuadTreeMask implements MaskInterface
{
    /** @var QuadTree */
    protected $quadTree;

    /** @var array */
    protected $colliders = [];

    public function __construct(Box $bounds)
    {
        $this->quadTree = new QuadTree($bounds);
    }

    protected function getBoxUid(Box $box)
    {
        return md5($box->serialize());
    }

    /**
     * @param Box $box
     * @return MaskInterface
     */
    public function insert(ColliderInterface $collider): MaskInterface
    {
        $bb = $collider->getBoundingBox();
        $this->quadTree->insert($bb);
        $this->colliders[$this->getBoxUid($bb)] = $collider;
        return $this;
    }

    /** @return int */
    public function count(): int
    {
        return count($this->colliders);
    }

    /** @return array */
    public function all(): array
    {
        return array_values($this->colliders);
    }

    /** @return Box */
    public function getBounds(): Box
    {
        return $this->quadTree->getBounds();
    }

    /**
     * @param Box $box
     * @return array
     */
    public function getCollisions(ColliderInterface $collider): array
    {
        $colliders = [];
        $boxes = $this->quadTree->getCollisions($collider->getBoundingBox());

        foreach ($boxes as $box) {
            $colliders[] = $this->colliders[$this->getBoxUid($box)];
        }

        return $colliders;
    }

    /**
     * @param Box $box
     * @return bool
     */
    public function collides(ColliderInterface $collider): bool
    {
        return $this->quadTree->collides($collider->getBoundingBox());
    }
}
