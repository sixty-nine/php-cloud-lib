<?php

namespace SixtyNine\Cloud\Command;

use SixtyNine\Cloud\Builder\FiltersBuilder;
use SixtyNine\Cloud\Builder\WordsListBuilder;
use SixtyNine\Cloud\Serializer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportUrlCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('import-url')
            ->setDescription('Create a words list from a URL')
            ->addArgument('url', InputArgument::REQUIRED, 'The URL for the words')
            ->addOption('case', null, InputOption::VALUE_OPTIONAL, 'Change case filter type (uppercase, lowercase, ucfirst)')
            ->addOption('max-count', null, InputOption::VALUE_OPTIONAL, 'Maximum number of words', 100)
            ->addOption('min-length', null, InputOption::VALUE_OPTIONAL, 'Minimumal word length', 5)
            ->addOption('max-length', null, InputOption::VALUE_OPTIONAL, 'Maximal word length', 10)
            ->addOption('no-remove-numbers', null, InputOption::VALUE_NONE, 'Disable the remove numbers filter')
            ->addOption('no-remove-trailing', null, InputOption::VALUE_NONE, 'Disable the remove trailing characters filter')
            ->addOption('no-remove-unwanted', null, InputOption::VALUE_NONE, 'Disable the remove unwanted characters filter')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filtersBuilder = FiltersBuilder::create()
            ->setMinLength($input->getOption('min-length'))
            ->setMaxLength($input->getOption('max-length'))
            ->setRemoveNumbers(!$input->getOption('no-remove-numbers'))
            ->setRemoveUnwanted(!$input->getOption('no-remove-unwanted'))
            ->setRemoveTrailing(!$input->getOption('no-remove-trailing'))
        ;

        $case = $input->getOption('case');

        if ($case && in_array($case, $filtersBuilder->getAllowedCase())) {
            $filtersBuilder->setCase($case);
        }

        $list = WordsListBuilder::create()
            ->setMaxWords($input->getOption('max-count'))
            ->setFilters($filtersBuilder->build())
            ->importUrl($input->getArgument('url'))
            ->build('list')
        ;

        $serializer = new Serializer();
        $json = $serializer->saveList($list, true);
        echo $json . PHP_EOL;
    }
}
