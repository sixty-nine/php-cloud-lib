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
    /** @var array */
    protected $fonts = array();

    /**
     * Initialize the FontsFactory.
     *
     * If $isFontPath is true then the first parameter is considered as a path to
     * a directory containing the fonts. This directory is scanned for TTF and OTF
     * fonts.
     *
     * If $isFontPath is false, then the first parameter must contain a path to
     * a TTF/ODF font, or must be an array of those paths.
     *
     * @param string|array $fonts
     * @param bool $isFontPath
     */
    protected function __construct($fonts, $isFontPath = true)
    {
        if ($isFontPath) {
            $this->loadFonts($fonts);
            return;
        }

        $fonts = is_array($fonts) ? $fonts : [$fonts];
        foreach ($fonts as $font) {
            $this->addFont($font);
        }
    }

    /**
     * @param string $fonts
     * @param bool $isFontPath
     * @return FontsFactory
     */
    public static function create($fonts, $isFontPath = true)
    {
        return new self($fonts, $isFontPath);
    }

    /**
     * @param string $fontsPath
     */
    public function loadFonts($fontsPath)
    {
        Assert::fileExists($fontsPath, 'The fonts path must exist');
        foreach (glob($fontsPath . '/*.{ttf,otf}', GLOB_BRACE) as $filename) {
            $this->addFont($filename);
        }
    }

    /**
     * @param string $filename
     */
    protected function addFont($filename)
    {
        Assert::fileExists($filename, 'The font file must exist');
        $name = basename($filename);
        $this->fonts[$name] = realpath($filename);
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
