<?php

namespace SixtyNine\Cloud\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseCloudCommand extends Command
{
    protected function configure()
    {
        $this
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
}
