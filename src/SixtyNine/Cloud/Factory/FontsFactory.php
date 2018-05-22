<?php


namespace SixtyNine\Cloud\Factory;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Color;
use Imagine\Image\Point;
use Imagine\Gd\Font as ImagineFont;
use SixtyNine\Cloud\Model\Font;
use Webmozart\Assert\Assert;

class FontsFactory
{
    /** @var string */
    protected $fontsPath;
    /** @var array */
    protected $fonts = array();

    /**
     * @param string $fontsPath
     */
    protected function __construct($fontsPath)
    {
        $this->fontsPath = $fontsPath;
        $this->loadFonts();
    }

    /**
     * @param string $fontsPath
     * @return FontsFactory
     */
    public  static function create($fontsPath)
    {
        Assert::fileExists($fontsPath, 'The fonts path must exist');
        return new self($fontsPath);
    }

    public function loadFonts()
    {
        foreach (glob($this->fontsPath . '/*.{ttf,otf}', GLOB_BRACE) as $filename) {
            $name = basename($filename);
            $this->fonts[$name] = realpath($filename);
        }
    }

    /**
     * @param string $name
     * @return Font
     * @throws \InvalidArgumentException
     */
    public function getFont($name)
    {
        Assert::keyExists($this->fonts, $name, 'Font not found: ' . $name);
        return new Font($name, $this->fonts[$name]);
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

    /**
     * @return array
     */
    public function getFonts()
    {
        return array_keys($this->fonts);
    }
}
