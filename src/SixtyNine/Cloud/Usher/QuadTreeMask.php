<?php

namespace SixtyNine\Cloud\Usher;

use Imagine\Image\PointInterface;
use SixtyNine\Cloud\Factory\Logger;
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
        $this->logger = Logger::getInstance();
        $this->tree = new QuadTree(new Box(0, 0, $width, $height));
    }

    public function getBoxes()
    {
        return $this->tree->getAllObjects();
    }

    /**
     * @param \Imagine\Image\PointInterface $position
     * @param Box $box
     */
    public function add(PointInterface $position, Box $box)
    {
        $box = $box->move($position->getX(), $position->getY());

        $this->logger->log('  Box added to mask ' . $box, Logger::DEBUG);

        $this->tree->insert($box);
    }

    /**
     * @param Box $box
     * @return bool
     */
    public function overlaps(Box $box)
    {
        $collides = $this->tree->collides($box);
        $this->logger->log(sprintf(
            '  Overlap test %s --> %s', $box, $collides ? 'COLLISION' : 'NO COLLISION'
        ), Logger::DEBUG);

        return $collides;
    }
}
