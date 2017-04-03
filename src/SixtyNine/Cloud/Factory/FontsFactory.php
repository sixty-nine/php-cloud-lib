<?php


namespace SixtyNine\Cloud\Factory;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Color;
use Imagine\Image\Point;
use Imagine\Gd\Font as ImagineFont;
use SixtyNine\Cloud\Model\Font;

class FontsFactory
{
    /** @var string */
    protected $fontsPath;
    /** @var array */
    protected $fonts = array();

    protected function __construct($fontsPath)
    {
        $this->fontsPath = $fontsPath;
        $this->loadFonts();
    }

    public  static function create($fontsPath)
    {
        if (!file_exists($fontsPath)) {
            throw new \InvalidArgumentException('The fonts path must exist');
        }

        return new self($fontsPath);
    }

    public function loadFonts()
    {
        foreach (glob($this->fontsPath . "/*.ttf") as $filename) {
            $name = basename($filename);
            $this->fonts[$name] = new Font($name, realpath($filename));
        }
    }

    /**
     * @param string $name
     * @return Font
     * @throws \InvalidArgumentException
     */
    public function getFont($name)
    {
        if (!array_key_exists($name, $this->fonts)) {
            throw new \InvalidArgumentException('Font not found: ' . $name);
        }

        return $this->fonts[$name];
    }

    /**
     * @param string $name
     * @param int $size
     * @param string $color
     * @return ImagineFont
     */
    public function getImagineFont($name, $size, $color = '#000000')
    {
        $font = $this->getFont($name);
        return new ImagineFont($font->getFile(), $size, new Color($color));
    }

    public function getFonts()
    {
        return array_keys($this->fonts);
    }
}
