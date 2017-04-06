<?php

namespace SixtyNine\Cloud\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuildFromUrlCommand extends BaseCloudCommand
{
    /** {@inheritdoc} */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('cloud:from-url')
            ->setDescription('Create a cloud from a URL')
            ->addArgument('url', InputArgument::REQUIRED, 'The URL for the words')
        ;
    }

    /** {@inheritdoc} */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = new CommandsHelper();
        $timing = $helper->createCloud('from-url', $input);
        $output->writeln(sprintf('Cloud generated in %s seconds', $timing->getDuration() / 1000));
    }
}
