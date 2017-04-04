<?php

namespace SixtyNine\Cloud\Model;

use Imagine\Image\BoxInterface;
use Imagine\Image\Point;
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
    public function intersects(Box $box)
    {
        return ($this->getLeft() < $box->getRight()
            && $this->getRight() > $box->getLeft()
            && $this->getTop() < $box->getBottom()
            && $this->getBottom() > $box->getTop()
        );
    }

    /**
     * @param Box $box
     * @return bool
     */
    public function inside(Box $box)
    {
        return ($this->getLeft() >= $box->getLeft()
            && $this->getRight() <= $box->getRight()
            && $this->getTop() >= $box->getTop()
            && $this->getBottom() <= $box->getBottom()
        );
    }

    /**
     * @param int $deltaX
     * @param int $deltaY
     * @return \SixtyNine\Cloud\Model\Box
     */
    public function move($deltaX, $deltaY)
    {
        return new self($this->getX() + $deltaX, $this->getY() + $deltaY, $this->getWidth(), $this->getHeight());
    }

    public function resize($count)
    {
        return new self(
            $this->getX() - $count,
            $this->getY() - $count,
            $this->getWidth() + 2 * $count,
            $this->getHeight() + 2 * $count
        );
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

    /**
     * @return Point
     */
    public function getPosition()
    {
        return new Point($this->getX(), $this->getY());
    }

    /**
     * @return \Imagine\Image\Box
     */
    public function getDimensions()
    {
        return new \Imagine\Image\Box($this->getWidth(), $this->getHeight());
    }

    function __toString()
    {
        return sprintf('(%s, %s) x (%s, %s)', $this->x, $this->y, $this->width, $this->height);
    }
}

