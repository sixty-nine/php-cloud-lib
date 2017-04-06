<?php

namespace SixtyNine\Cloud\Command;

use Imagine\Gd\Image;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Color;
use SixtyNine\Cloud\Factory\FontsFactory;
use SixtyNine\Cloud\Factory\PlacerFactory;
use SixtyNine\Cloud\Model\Cloud;
use SixtyNine\Cloud\Renderer\CloudRenderer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DebugUsherCommand extends Command
{
    /** {@inheritdoc} */
    protected function configure()
    {
        $this
            ->setName('debug:usher')
            ->setDescription('Show the path a word placer uses')
            ->addArgument('placer', InputArgument::REQUIRED, 'The name of the words placer')
            ->addOption('width', null, InputOption::VALUE_OPTIONAL, 'Width of the image', 800)
            ->addOption('height', null, InputOption::VALUE_OPTIONAL, 'Height of the image', 600)
            ->addOption('color', null, InputOption::VALUE_OPTIONAL, 'Color of the path', '#FF0000')
            ->addOption('background-color', null, InputOption::VALUE_OPTIONAL, 'Background color of the cloud', '#FFFFFF')
            ->addOption('save-to-file', null, InputOption::VALUE_OPTIONAL, 'If set to a file name, the output will be saved there')
            ->addOption('format', null, InputOption::VALUE_OPTIONAL, 'Output format (gif, jpeg, png)', 'png')
        ;
    }

    /** {@inheritdoc} */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cloud = (new Cloud())
            ->setWidth($input->getOption('width'))
            ->setHeight($input->getOption('height'))
            ->setBackgroundColor($input->getOption('background-color'))
        ;
        $helper = new CommandsHelper();
        $renderer = new CloudRenderer($cloud, FontsFactory::create(BASE_PATH . '/fonts'));
        $placerName = $helper->getPlacer($input->getArgument('placer'));

        $imagine = new Imagine();
        $image = $imagine->create(
            new Box(
                $input->getOption('width'),
                $input->getOption('height')
            ),
            new Color(
                $input->getOption('background-color')
            )
        );

        $placer = PlacerFactory::getInstance()->getPlacer(
            $placerName,
            $input->getOption('width'),
            $input->getOption('height')
        );

        $renderer->renderUsher($placer, $input->getOption('color'));

        $helper->output($renderer->getImage(), $input->getOption('format'), $input->getOption('save-to-file'));
    }
}
