<?php

namespace SixtyNine\Cloud\Drawer;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Color;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
use SixtyNine\DataTypes\Box as MyBox;
use SixtyNine\Cloud\Factory\FontsFactory;
use SixtyNine\Cloud\Usher\MaskInterface;
use Webmozart\Assert\Assert;

class Drawer
{
    /** @var \SixtyNine\Cloud\Factory\FontsFactory */
    protected $fontsFactory;

    /** @var \Imagine\Gd\Imagine */
    protected $imagine;

    /** @var ImageInterface */
    protected $image;

    /** @var string */
    protected $font;

    /**
     * @param FontsFactory $fontsFactory
     */
    protected function __construct(FontsFactory $fontsFactory)
    {
        $this->imagine = new Imagine();
        $this->fontsFactory = $fontsFactory;
    }

    /**
     * @param \SixtyNine\Cloud\Factory\FontsFactory $fontsFactory
     * @return Drawer
     */
    public static function create(FontsFactory $fontsFactory)
    {
        return new self($fontsFactory);
    }

    /**
     * @param int $width
     * @param int $height
     * @param string $color
     * @return Drawer
     */
    public function createImage($width, $height, $color = '#FFFFFF')
    {
        $this->image = $this->imagine->create(
            new Box($width, $height),
            new Color($color)
        );
        return $this;
    }

    /**
     * @param string $fontName
     * @return Drawer
     */
    public function setFont($fontName)
    {
        $this->font = $fontName;
        return $this;
    }

    /**
     * @param int $x
     * @param int $y
     * @param string $text
     * @param int $size
     * @param string $color
     * @param int $angle
     * @throws \InvalidArgumentException
     * @return Drawer
     */
    public function drawText($x, $y, $text, $size, $color = '#000000', $angle = 0)
    {
        Assert::notNull($this->font, 'Font not set');
        $font = $this->fontsFactory->getImagineFont($this->font, $size, $color);
        $this->image->draw()->text($text, $font, new Point($x , $y), $angle);

        return $this;
    }

    /**
     * @param int $x
     * @param int $y
     * @param int $width
     * @param int $height
     * @param string $color
     * @return Drawer
     */
    public function drawBox($x, $y, $width, $height, $color = '#000000')
    {
        if ($x < 0) {
            $width += $x;
            $x = 0;
        }

        if ($y < 0) {
            $height += $y;
            $y = 0;
        }

        $points = array(
            new Point($x, $y),
            new Point($x + $width, $y),
            new Point($x + $width, $y + $height),
            new Point($x, $y + $height),
        );

        $this->image->draw()->polygon($points, new Color($color));

        return $this;
    }

    /**
     * @param int $x
     * @param int $y
     * @param int $width
     * @param int $height
     * @param int $angle
     * @param string $color
     * @return Drawer
     */
    public function drawBoxForText($x, $y, $width, $height, $angle, $color = '#000000')
    {
        $box = self::getBoxFoxText($x, $y, $width, $height, $angle);
        return $this->drawBox($box->getX(), $box->getY(), $box->getWidth(), $box->getHeight(), $color);
    }

    /**
     * @param int $x
     * @param int $y
     * @param int $width
     * @param int $height
     * @param int $angle
     * @return MyBox
     */
    public static function getBoxFoxText($x, $y, $width, $height, $angle)
    {
        switch ($angle) {
            case 90:
                return new MyBox($x, $y + $height, $height, $width);
            case 180:
                return new MyBox($x - $width, $y + $height, $width, $height);
            case 270:
                return new MyBox($x - $height, $y - $width + $height, $height, $width);
        }

        return new MyBox($x, $y, $width, $height);
    }

    /**
     * @param MaskInterface $mask
     * @param string $color
     * @return $this
     */
    public function drawMask(MaskInterface $mask, $color = '#ffffff')
    {
        foreach ($mask->getBoxes() as $box) {
            $this->drawBox($box->getX(), $box->getY(), $box->getWidth(), $box->getHeight(), $color);
        }
        return $this;
    }

    /**
     * @return ImageInterface
     */
    public function getImage()
    {
        Assert::notNull($this->image, 'Image not created');
        return $this->image;
    }
}
