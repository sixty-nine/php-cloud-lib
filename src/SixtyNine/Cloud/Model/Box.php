<?php

namespace SixtyNine\Cloud\Model;

use Imagine\Image\BoxInterface;
use Imagine\Image\Point;
use Imagine\Image\PointInterface;
use JMS\Serializer\Annotation as JMS;

/**
 * An axis-aligned rectangle with collision detection
 */
class Box
{
    /**
     * @var float
     * @JMS\Type("float")
     */
    protected $x;

    /**
     * @var float
     * @JMS\Type("float")
     */
    protected $y;

    /**
     * @var float
     * @JMS\Type("float")
     */
    protected $width;

    /**
     * @var float
     * @JMS\Type("float")
     */
    protected $height;

    /**
     * @var float
     * @JMS\Exclude()
     */
    protected $top;

    /**
     * @var float
     * @JMS\Exclude()
     */
    protected $bottom;

    /**
     * @var float
     * @JMS\Exclude()
     */
    protected $left;

    /**
     * @var float
     * @JMS\Exclude()
     */
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

    public function update()
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
     * @param float $deltaX
     * @param float $deltaY
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
     * @return float
     */
    public function getBottom()
    {
        return $this->bottom;
    }

    /**
     * @return float
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @return float
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @return float
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * @return float
     */
    public function getTop()
    {
        return $this->top;
    }

    /**
     * @return float
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return float
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * @return float
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

