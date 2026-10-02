<?php

namespace SixtyNine\Cloud\Command;

use SixtyNine\Cloud\Factory\FontsFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListFontsCommand extends Command
{
    /** {@inheritdoc} */
    protected function configure(): void
    {
        $this
            ->setName('list:fonts')
            ->setDescription('List all the TTF fonts')
            ->addOption('fonts-path', null, InputOption::VALUE_OPTIONAL, 'Optional path to the fonts, if omitted, defaults to <base>/fonts')
        ;
    }

    /** {@inheritdoc} */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fontsPath = $input->getOption('fonts-path')
            ? realpath($input->getOption('fonts-path'))
            : constant('BASE_PATH') . '/fonts'
        ;

        $output->writeln(sprintf('Fonts found in "%s":', $fontsPath));
        $factory = FontsFactory::create($fontsPath);
        $fonts = $factory->getFonts();

        if (!count($fonts)) {
            $output->writeln('  No fonts found');
            return 0;
        }

        foreach ($fonts as $name) {
            $output->writeln(sprintf('  - %s', $name));
        }

        return 0;
    }
}
