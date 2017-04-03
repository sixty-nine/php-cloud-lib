<?php

namespace SixtyNine\Cloud\Model;

use Imagine\Image\BoxInterface;
use Imagine\Image\PointInterface;

/**
 * An axis-aligned rectangle with collision detection
 */
class Box
{
    /** @var int */
    protected $x;
    /** @var int */
    protected $y;
    /** @var int */
    protected $width;
    /** @var int */
    protected $height;
    /** @var int */
    protected $top;
    /** @var int */
    protected $bottom;
    /** @var int */
    protected $left;
    /** @var int */
    protected $right;

    public function __construct($x, $y, $width, $height)
    {
        $this->x = $x;
        $this->y = $y;
        $this->width = $width;
        $this->height = $height;

        $this->update();
    }

    public static function constructFromImagine(PointInterface $point, BoxInterface $box)
    {
        return new self($point->getX(), $point->getY(), $box->getWidth(), $box->getHeight());
    }

    protected function update()
    {
        $this->left = $this->x;
        $this->right = $this->x + $this->width;
        $this->top = $this->y;
        $this->bottom = $this->y + $this->height;
    }

    /**
     * Detect box collision
     * This algorithm only works with Axis-Aligned boxes!
     * @param Box $box The other rectangle to test collision with
     * @return boolean True is the boxes collide, false otherwise
     */
    function intersects(Box $box)
    {
        return ($this->getLeft() < $box->getRight() &&
           $this->getRight() > $box->getLeft() &&
           $this->getTop() < $box->getBottom() &&
           $this->getBottom() > $box->getTop())
        ;
    }

    /**
     * @return int
     */
    public function getBottom()
    {
        return $this->bottom;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @return int
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @return int
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * @return int
     */
    public function getTop()
    {
        return $this->top;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * @return int
     */
    public function getY()
    {
        return $this->y;
    }
}

