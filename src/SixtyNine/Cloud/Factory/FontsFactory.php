<?php


namespace SixtyNine\Cloud\Factory;

use Imagine\Image\Color;
use Imagine\Gd\Font as ImagineFont;
use SixtyNine\Cloud\Model\Font;
use Webmozart\Assert\Assert;

class FontsFactory
{
    /** @var string|array */
    private $fontsPath;

    /** @var array */
    protected $fonts = array();

    /**
     * @param string|array $fontsPath string to scan directory with .ttf file extension or array with full path to each font
     */
    protected function __construct($fontsPath)
    {
        $this->fontsPath = $fontsPath;
        $this->loadFonts();
    }

    /**
     * @param string|array $fontsPath string to scan directory with .ttf file extension or array with full path to each font
     * @return FontsFactory
     */
    public static function create($fontsPath)
    {
        return new self($fontsPath);
    }

    public function loadFonts()
    {
        $fontsPath = $this->fontsPath;
        if (is_string($fontsPath)) {
            Assert::fileExists($fontsPath, 'The fonts path must exist');
            $fontsPath = glob($fontsPath . "/*.ttf");
        }
        foreach ($fontsPath as $filename) {
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
        Assert::keyExists($this->fonts, $name, 'Font not found: ' . $name);
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

    /**
     * @return array
     */
    public function getFonts()
    {
        return array_keys($this->fonts);
    }
}
