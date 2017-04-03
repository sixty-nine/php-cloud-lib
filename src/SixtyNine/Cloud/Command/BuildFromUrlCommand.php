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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuildFromUrlCommand extends Command
{
    protected $allowedOutputFormats = array('gif', 'jpeg', 'png');
    protected $allowedFontSizeBoosts = array('linear', 'dim', 'boost');
    protected $allowedPaletteTypes = array('cycle', 'random');

    protected function configure()
    {
        $this
            ->setName('cloud:from-url')
            ->setDescription('Create a cloud from a URL')
            ->addArgument('url', InputArgument::REQUIRED, 'The URL for the words')
            // Filters options
            ->addOption('case', null, InputOption::VALUE_OPTIONAL, 'Change case filter type (uppercase, lowercase, ucfirst)')
            ->addOption('max-word-count', null, InputOption::VALUE_OPTIONAL, 'Maximum number of words', 100)
            ->addOption('min-word-length', null, InputOption::VALUE_OPTIONAL, 'Minimumal word length', 5)
            ->addOption('max-word-length', null, InputOption::VALUE_OPTIONAL, 'Maximal word length', 10)
            ->addOption('no-remove-numbers', null, InputOption::VALUE_NONE, 'Disable the remove numbers filter')
            ->addOption('no-remove-trailing', null, InputOption::VALUE_NONE, 'Disable the remove trailing characters filter')
            ->addOption('no-remove-unwanted', null, InputOption::VALUE_NONE, 'Disable the remove unwanted characters filter')
            // WordsList options
            ->addOption('vertical-probability', null, InputOption::VALUE_OPTIONAL, 'The percentage probability of having vertical words (0-100)', 50)
            ->addOption('palette', null, InputOption::VALUE_OPTIONAL, 'The name of the palette used to color words')
            ->addOption('palette-type', null, InputOption::VALUE_OPTIONAL, 'The way the palette colors are used (cycle, random)', 'cycle')
            ->addOption('palettes-file', null, InputOption::VALUE_OPTIONAL, 'Optional path to the fonts, if omitted, defaults to <base>/fonts')
            ->addOption('sort-by', null, InputOption::VALUE_OPTIONAL, 'Words sorting field (text, count, angle)')
            ->addOption('sort-order', null, InputOption::VALUE_OPTIONAL, 'Words sorting order (asc, desc)')
            // Cloud options
            ->addOption('background-color', null, InputOption::VALUE_OPTIONAL, 'Background color of the cloud', '#FFFFFF')
            ->addOption('placer', null, InputOption::VALUE_OPTIONAL, 'Word placer to use')
            ->addOption('font', null, InputOption::VALUE_OPTIONAL, 'Font to use to draw the cloud')
            ->addOption('width', null, InputOption::VALUE_OPTIONAL, 'Width of the cloud', 800)
            ->addOption('height', null, InputOption::VALUE_OPTIONAL, 'Height of the cloud', 600)
            ->addOption('font-size-boost', null, InputOption::VALUE_OPTIONAL, 'Minimal font size (linear, dim, boost)', 'linear')
            ->addOption('min-font-size', null, InputOption::VALUE_OPTIONAL, 'Minimal font size', 12)
            ->addOption('max-font-size', null, InputOption::VALUE_OPTIONAL, 'Maximal font size', 64)
            // Other options
            ->addOption('save-to-file', null, InputOption::VALUE_OPTIONAL, 'If set to a file name, the output will be saved there')
            ->addOption('format', null, InputOption::VALUE_OPTIONAL, 'Output format (gif, jpeg, png)', 'png')
            ->addOption('fonts-path', null, InputOption::VALUE_OPTIONAL, 'Optional path to the fonts, if omitted, defaults to <base>/fonts')
            ->addOption('render-usher', null, InputOption::VALUE_NONE, 'Enable the rendering of the words usher')
            ->addOption('render-boxes', null, InputOption::VALUE_NONE, 'Enable the rendering of the words bounding boxes')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Build the filters
        $filtersBuilder = FiltersBuilder::create()
            ->setMinLength($input->getOption('min-word-length'))
            ->setMaxLength($input->getOption('max-word-length'))
            ->setRemoveNumbers(!$input->getOption('no-remove-numbers'))
            ->setRemoveUnwanted(!$input->getOption('no-remove-unwanted'))
            ->setRemoveTrailing(!$input->getOption('no-remove-trailing'))
        ;

        $case = $input->getOption('case');
        if ($case && in_array($case, $filtersBuilder->getAllowedCase())) {
            $filtersBuilder->setCase($case);
        }

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
            ->importUrl($input->getArgument('url'))
        ;

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

    /**
     * @param FontsFactory $factory
     * @param string $font
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function getFont(FontsFactory $factory, $font)
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
     * @param string $name
     * @return PlacerInterface
     * @throws \InvalidArgumentException
     */
    protected function getPlacer($name)
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
     * @param string $paletteName
     * @param string $paletteType
     * @param string $palettesFile
     * @return bool|ColorGeneratorInterface
     * @throws \InvalidArgumentException
     */
    protected function getColorGenerator($paletteName, $paletteType, $palettesFile = null)
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
     * @param string $type
     * @return FontSizeGeneratorInterface
     * @throws \InvalidArgumentException
     */
    protected function getFontSizeGenerator($type = 'linear')
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
    /**
     * @param ImageInterface $image
     * @param string $outputFormat
     * @param string $outputFile
     * @throws \InvalidArgumentException
     */
    protected function output(ImageInterface $image, $outputFormat, $outputFile = null)
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
