<?php

namespace SixtyNine\Cloud\Command;

use SixtyNine\Cloud\Builder\PalettesBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListPalettesCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('list:palettes')
            ->setDescription('List all available palettes')
            ->addOption('palettes-file', null, InputOption::VALUE_OPTIONAL, 'Optional path to the fonts, if omitted, defaults to <base>/fonts')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $palettesFile = __DIR__ . '/../Resources/palettes.yml';
        if ($input->getOption('palettes-file')) {
            $palettesFile = $input->getOption('palettes-file');
        }
        $builder = PalettesBuilder::create()->importPalettes($palettesFile);

        $output->writeln('Palettes found:');
        $palettes = $builder->getPalettes();

        if (!count($palettes)) {
            $output->writeln('  No palette found');
            return;
        }

        foreach ($palettes as $palette) {
            $output->writeln(sprintf('  - %s', $palette->getName()));
        }
    }
}
