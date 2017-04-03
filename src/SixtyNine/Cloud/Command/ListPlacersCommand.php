<?php

namespace SixtyNine\Cloud\Command;

use SixtyNine\Cloud\Factory\PlacerFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListPlacersCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('list:placers')
            ->setDescription('List all word placers')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $placers = PlacerFactory::getInstance()->getPlacersNames();
        $output->write('Available words placers: ');

        if (!count($placers)) {
            $output->writeln('none');
            return;
        }


        $output->writeln(join(', ', $placers));
    }
}
