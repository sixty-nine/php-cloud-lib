<?php

namespace SixtyNine\Cloud\Command;

use Imagine\Image\ImageInterface;
use SixtyNine\Cloud\Builder\PalettesBuilder;
use SixtyNine\Cloud\Color\ColorGeneratorInterface;
use SixtyNine\Cloud\Color\RandomColorGenerator;
use SixtyNine\Cloud\Color\RotateColorGenerator;
use SixtyNine\Cloud\Factory\FontsFactory;
use SixtyNine\Cloud\Factory\PlacerFactory;
use SixtyNine\Cloud\FontSize\BoostFontSizeGenerator;
use SixtyNine\Cloud\FontSize\DimFontSizeGenerator;
use SixtyNine\Cloud\FontSize\FontSizeGeneratorInterface;
use SixtyNine\Cloud\FontSize\LinearFontSizeGenerator;
use SixtyNine\Cloud\Placer\PlacerInterface;

class CommandsHelper
{
    protected $allowedFontSizeBoosts = array('linear', 'dim', 'boost');
    protected $allowedPaletteTypes = array('cycle', 'random');
    protected $allowedOutputFormats = array('gif', 'jpeg', 'png');

    /**
     * @param string $name
     * @return PlacerInterface
     * @throws \InvalidArgumentException
     */
    public function getPlacer($name)
    {
        $availablePlacers = PlacerFactory::getInstance()->getPlacersNames();

        if ($name) {
            if (!in_array($name, $availablePlacers)) {
                throw new \InvalidArgumentException('Word placer not found: ' . $name);
            }
            return $name;
        }

        if (!count($availablePlacers)) {
            throw new \InvalidArgumentException('No word placers available');
        }

        return $availablePlacers[0];
    }

    /**
     * @param FontsFactory $factory
     * @param string $font
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getFont(FontsFactory $factory, $font)
    {
        if ($font) {
            return $font;
        }

        if (0 === count($factory->getFonts())) {
            throw new \InvalidArgumentException('No font file found');
        }

        return $factory->getFonts()[0];
    }

    /**
     * @param string $type
     * @return FontSizeGeneratorInterface
     * @throws \InvalidArgumentException
     */
    public function getFontSizeGenerator($type = 'linear')
    {
        if (!in_array($type, $this->allowedFontSizeBoosts)) {
            throw new \InvalidArgumentException('Invalid font size boost: ' . $type);
        }

        $generatorClass = LinearFontSizeGenerator::class;
        if ($type === 'dim') {
            $generatorClass = DimFontSizeGenerator::class;
        }
        if ($type === 'boost') {
            $generatorClass = BoostFontSizeGenerator::class;
        }

        return new $generatorClass();
    }

    /**
     * @param string $paletteName
     * @param string $paletteType
     * @param string $palettesFile
     * @return bool|ColorGeneratorInterface
     * @throws \InvalidArgumentException
     */
    public function getColorGenerator($paletteName, $paletteType, $palettesFile = null)
    {
        if ($paletteName && $paletteType) {

            if (!in_array($paletteType, $this->allowedPaletteTypes)) {
                throw new \InvalidArgumentException('Palette type must be either "cycle" or "random"');
            }

            $file = $palettesFile
                ? $palettesFile
                : __DIR__ . '/../Resources/palettes.yml'
            ;
            $paletteBuilder = PalettesBuilder::create()->importPalettes($file);

            $palette = $paletteBuilder->getNamedPalette($paletteName);
            $generatorClass = ($paletteType === 'cycle')
                ? RotateColorGenerator::class
                : RandomColorGenerator::class
            ;
            return new $generatorClass($palette);
        }

        return false;
    }

    /**
     * @param ImageInterface $image
     * @param string $outputFormat
     * @param string $outputFile
     * @throws \InvalidArgumentException
     */
    public function output(ImageInterface $image, $outputFormat, $outputFile = null)
    {
        if (!in_array($outputFormat, $this->allowedOutputFormats)) {
            throw new \InvalidArgumentException('Invalid output format: ' . $outputFormat);
        }

        if ($outputFile) {
            $image->save($outputFile, array('format' => $outputFormat));
            return;
        }

        echo $image->get($outputFormat);
    }
}
