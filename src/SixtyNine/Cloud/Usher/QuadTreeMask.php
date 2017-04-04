<?php

namespace SixtyNine\Cloud\Usher;

use Imagine\Image\PointInterface;
use SixtyNine\Cloud\Model\Box;
use SixtyNine\Cloud\Model\QuadTree;

class QuadTreeMask implements MaskInterface
{
    /** @var QuadTree */
    protected $tree;

    /**
     * @param int $width
     * @param int $height
     */
    public function __construct($width, $height)
    {
        $this->tree = new QuadTree(new Box(0, 0, $width, $height));
    }


    /**
     * @param \Imagine\Image\PointInterface $position
     * @param Box $box
     */
    public function add(PointInterface $position, Box $box)
    {
        $box = $box->move($position->getX(), $position->getY());
        $this->tree->insert($box);
    }

    /**
     * @param Box $box
     * @return bool
     */
    public function overlaps(Box $box)
    {
        return $this->tree->collides($box);
    }
}
