<?php

namespace SixtyNine\Cloud\Command;

use Imagine\Image\ImageInterface;
use SixtyNine\Cloud\Builder\CloudBuilder;
use SixtyNine\Cloud\Builder\FiltersBuilder;
use SixtyNine\Cloud\Builder\PalettesBuilder;
use SixtyNine\Cloud\Builder\WordsListBuilder;
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
use SixtyNine\Cloud\Renderer\CloudRenderer;
use Symfony\Component\Console\Input\InputInterface;

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

    /**
     * @param CloudBuilder $cloudBuilder
     * @param FontsFactory $factory
     * @param PlacerInterface $placer
     * @param bool $renderBoxes
     * @return \Imagine\Gd\Image|ImageInterface
     */
    protected function render(CloudBuilder $cloudBuilder, FontsFactory $factory, PlacerInterface $placer = null, $renderBoxes = false)
    {
        $renderer = new CloudRenderer();
        $image = $renderer->render($cloudBuilder->build(), $factory, $renderBoxes);

        if ($placer) {
            $renderer->renderUsher($image, $placer, '#FF0000');
        }

        return $image;
    }

    public function getFilterBuilder(
        $minWordLength,
        $maxWordLength,
        $changeCase = null,
        $noRemoveNumbers = false,
        $noRemoveUnwanted = false,
        $noRemoveTrailing = false
    ) {
        $filtersBuilder = FiltersBuilder::create()
            ->setMinLength($minWordLength)
            ->setMaxLength($maxWordLength)
            ->setRemoveNumbers(!$noRemoveNumbers)
            ->setRemoveUnwanted(!$noRemoveTrailing)
            ->setRemoveTrailing(!$noRemoveUnwanted)
        ;

        if ($changeCase && in_array($changeCase, $filtersBuilder->getAllowedCase())) {
            $filtersBuilder->setCase($changeCase);
        }

        return $filtersBuilder;
    }

    /**
     * @param string $type
     * @param InputInterface $input
     * @throws \InvalidArgumentException
     */
    public function createCloud($type, InputInterface $input)
    {
        if (!in_array($type, array('from-url', 'from-file'))) {
            throw new \InvalidArgumentException('Invalid type for createCloud: ' . $type);
        }

        // Build the filters
        $filtersBuilder = $this->getFilterBuilder(
            $input->getOption('min-word-length'),
            $input->getOption('max-word-length'),
            $input->getOption('case'),
            $input->getOption('no-remove-numbers'),
            $input->getOption('no-remove-unwanted'),
            $input->getOption('no-remove-trailing')
        );

        // Create a placer
        $placerName = $this->getPlacer($input->getOption('placer'));
        $placer = PlacerFactory::getInstance()->getPlacer(
            $placerName,
            $input->getOption('width'),
            $input->getOption('height')
        );

        // Get the font file
        $fontsPath = $input->getOption('fonts-path')
            ? realpath($input->getOption('fonts-path'))
            : constant('BASE_PATH') . '/fonts'
        ;
        $factory = FontsFactory::create($fontsPath);
        $font = $this->getFont($factory, $input->getOption('font'));

        // Create the list builder
        $listBuilder = WordsListBuilder::create()
            ->setMaxWords($input->getOption('max-word-count'))
            ->setFilters($filtersBuilder->build())
            ->randomizeOrientation($input->getOption('vertical-probability'))
        ;

        if ($type === 'from-file') {
            $file = $input->getArgument('file');
            if (!file_exists($file)) {
                throw new \InvalidArgumentException('File not found: ' . $file);
            }
            $listBuilder->importWords(file_get_contents($file));
        } else {
            $listBuilder->importUrl($input->getArgument('url'));
        }

        $sortBy = $input->getOption('sort-by');
        $sortOrder = $input->getOption('sort-order');

        if ($sortBy && $sortOrder) {
            $listBuilder->sort($sortBy, $sortOrder);
        }

        // Apply a color generator if needed
        $colorGenerator = $this->getColorGenerator(
            $input->getOption('palette'),
            $input->getOption('palette-type'),
            $input->getOption('palettes-file')
        );

        if ($colorGenerator) {
            $listBuilder->randomizeColors($colorGenerator);
        }

        // Build the list
        $list = $listBuilder->build('list');

        // Create a cloud builder
        $cloudBuilder = CloudBuilder::create($factory)
            ->setBackgroundColor($input->getOption('background-color'))
            ->setDimension($input->getOption('width'), $input->getOption('height'))
            ->setFont($font)
            ->setFontSizes($input->getOption('min-font-size'), $input->getOption('max-font-size'))
            ->setPlacer($placerName)
            ->setSizeGenerator($this->getFontSizeGenerator($input->getOption('font-size-boost')))
            ->useList($list)
        ;

        // Render the cloud and show the bounding boxes and the usher if needed
        $image = $this->render(
            $cloudBuilder,
            $factory,
            $input->getOption('render-usher') ? $placer : null,
            $input->getOption('render-boxes')
        );

        $this->output($image, $input->getOption('format'), $input->getOption('save-to-file'));
    }
}
